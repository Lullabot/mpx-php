<?php

namespace Mpx\Service;

use GuzzleHttp\Event\HasEmitterTrait;
use GuzzleHttp\Psr7\Uri;
use Mpx\ClientInterface;
use Mpx\HasCachePoolTrait;
use Mpx\HasClientTrait;
use Mpx\HasLoggerTrait;
use Mpx\Object;
use Mpx\UserInterface;
use Mpx\Event\ObjectLoadEvent;
use Mpx\Exception\NotificationsUnsupportedException;
use Mpx\Exception\ObjectNotFoundException;
use Pimple\Container;
use Psr\Log\LoggerInterface;
use Stash\Interfaces\PoolInterface;

class ObjectService implements ObjectServiceInterface {
  use HasCachePoolTrait;
  use HasClientTrait;
  use HasEmitterTrait;
  use HasLoggerTrait;

  /** @var \Mpx\UserInterface */
  protected $user;

  /** @var \Psr\Http\Message\UriInterface */
  protected $uri;

  /** @var \Psr\Http\Message\UriInterface */
  protected $readOnlyUri;

  /** @var array */
  protected $staticCache = array();

  /** @var array */
  protected $pidMapping;

  /** @var string */
  protected $objectClass;

  /** @var string */
  protected $objectType;

  /** @var string */
  protected $schema;

  /** @var \Mpx\Service\ObjectNotificationService */
  protected $notificationService;

  /**
   * Construct an mpx object service.
   *
   * @param string $objectClass
   * @param \Mpx\UserInterface $user
   * @param \Mpx\ClientInterface $client
   * @param \Stash\Interfaces\PoolInterface $cache
   * @param \Psr\Log\LoggerInterface $logger
   *
   * @throws \InvalidArgumentException
   */
  public function __construct($objectClass, UserInterface $user, ClientInterface $client = NULL, PoolInterface $cache = NULL, LoggerInterface $logger = NULL) {
    $interfaces = class_implements($objectClass);
    if (!in_array('Mpx\\Object\\ObjectInterface', $interfaces)) {
      throw new \InvalidArgumentException("Class $objectClass does not extend Mpx\\Object\\ObjectInterface.");
    }

    $this->objectClass = $objectClass;
    $this->objectType = call_user_func(array($objectClass, 'getType'));
    $this->uri = call_user_func(array($objectClass, 'getUri'));
    $this->readOnlyUri = call_user_func(array($objectClass, 'getReadOnlyUri'));

    $this->user = $user;
    $this->client = $client;
    $this->cachePool = clone $cache;
    $this->cachePool->setNamespace($this->cachePool->getNamespace() . $this->getCacheSubnamespace());
    $this->logger = $logger;
  }

  /**
   * @return string
   */
  private function getCacheSubnamespace() {
    // Add the object type and user ID to the namespace.
    return $this->objectType . $this->getUser()->getId();
  }

  /**
   * Create a new instance of an object service class.
   *
   * @param string $objectClass
   * @param \Mpx\UserInterface $user
   * @param \Pimple\Container $container
   *
   * @return static
   */
  public static function create($objectClass, UserInterface $user, Container $container) {
    return new static(
      $objectClass,
      $user,
      $container['client'],
      $container['cache'],
      $container['logger']
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getObjectType() {
    return $this->objectType;
  }

  /**
   * {@inheritdoc}
   */
  public function getUser() {
    return $this->user;
  }

  /**
   * {@inheritdoc}
   */
  public function getUri() {
    return $this->uri;
  }

  /**
   * {@inheritdoc}
   */
  public function fetch($id, array $options = []) {
    $objects = $this->fetchMultiple([$id], $options);
    if (empty($objects)) {
      throw new ObjectNotFoundException("Cannot load mpx {$this->objectType} {$id}.");
    }
    return reset($objects);
  }

  /**
   * {@inheritdoc}
   */
  public function fetchMultiple(array $ids, array $options = []) {
    $uri = $this->readOnlyUri;
    $uri = $uri->withPath($uri->getPath() . '/' . implode(',', $ids));

    $data = $this->getClient()->authenticatedRequest('GET', $uri, $this->getUser(), $options);

    // Normalize the data structure if only one result was returned.
    $data = isset($data['entries']) ? $data['entries'] : array($data);

    // Convert the data to objects.
    return $this->createObjects($data);
  }

  /**
   * {@inheritdoc}
   */
  public function getCount(array $options = []) {
    $options += ['query' => []];
    $options['query'] += ['count' => 'true', 'entries' => 'false'];

    $data = $this->getClient()->authenticatedRequest('GET', $this->readOnlyUri, $this->getUser(), $options);
    if (isset($data['totalResults']) && is_numeric($data['totalResults'])) {
      return intval($data['totalResults']);
    }
    else {
      throw new \RuntimeException("Unable to parse totalResults value from API.");
    }
  }

  /**
   * {@inheritdoc}
   */
  public function load($id) {
    // If a full URL was provided, get the last segment which is the ID.
    if (filter_var($id, FILTER_VALIDATE_URL)) {
      $id = basename($id);
    }

    $objects = $this->loadMultiple(array($id));
    if (!isset($objects[$id])) {
      throw new ObjectNotFoundException("Cannot load mpx {$this->objectType} with id {$id} for {$this->getUser()->getUsername()}.");
    }
    return $objects[$id];
  }

  /**
   * {@inheritdoc}
   */
  public function loadMultiple(array $ids) {
    if ($ids_to_load = array_diff($ids, array_keys($this->staticCache))) {
      foreach ($ids_to_load as $id) {
        $this->staticCache[$id] = $this->getCachePool()->getItem($id)->get();
      }
      $ids_to_load = array_diff($ids_to_load, array_keys(array_filter($this->staticCache)));
      if ($ids_to_load) {
        // Ensure we are not trying to fetch too many items at once.
        $batches = array_chunk($ids_to_load, 50);
        foreach ($batches as $batch) {
          $objects = $this->fetchMultiple($batch);

          // Allow subscribers to be able to alter the objects before saving
          // them to the cache.
          $this->getEmitter()->emit('load', new ObjectLoadEvent($this, $objects));

          // Save the objects in the static and regular cache.
          $this->setCache($objects);
        }
      }
    }

    $return = array();
    foreach ($ids as $id) {
      if (isset($this->staticCache[$id])) {
        $return[$id] = $this->staticCache[$id];
      }
    }
    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function fetchByPid($pid) {
    try {
      $object = $this->fetch('', ['query' => ['byPid' => $pid]]);
      return $object;
    }
    catch (ObjectNotFoundException $exception) {
      $exception->setMessage("Cannot load mpx {$this->objectType} with pid {$pid} for {$this->getUser()->getUsername()}.");
      throw $exception;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function loadbyPid($pid) {
    $id = $this->getPidMappingId($pid);
    if (!isset($id)) {
      try {
        $object = $this->fetchByPid($pid);
        $this->setCache(array($object));
        return $object;
      }
      catch (ObjectNotFoundException $exception) {
        $this->setPidMappingId($pid, FALSE);
        throw $exception;
      }
    }
    elseif ($id !== FALSE) {
      return $this->load($id);
    }
    else {
      throw new ObjectNotFoundException("Cannot load mpx {$this->objectType} with public ID {$pid} for {$this->getUser()->getUsername()}.");
    }
  }

  /**
   * @return array
   */
  private function &getPidMapping() {
    if (!isset($this->pidMapping)) {
      $item = $this->getCachePool()->getItem('pid_mapping');
      $this->pidMapping = $item->get();
      if ($item->isMiss()) {
        $this->pidMapping = array();
      }
    }
    return $this->pidMapping;
  }

  public function savePidMapping(array $mapping = NULL) {
    if (!isset($mapping)) {
      $mapping = $this->getPidMapping();
    }
    else {
      $this->pidMapping = $mapping;
    }
    $this->getCachePool()->getItem('pid_mapping')->set($mapping);
  }

  /**
   * @param string $pid
   *
   * @return string|false|null
   */
  private function getPidMappingId($pid) {
    $mapping = $this->getPidMapping();
    return isset($mapping[$pid]) ? $mapping[$pid] : NULL;
  }

  /**
   * @param string $pid
   * @param string|false $id
   *
   * @return bool
   */
  private function setPidMappingId($pid, $id) {
    $mapping = &$this->getPidMapping();
    $mapping[$pid] = $id;
  }

  /**
   * @param \Mpx\Object\ObjectInterface[] $objects
   */
  private function setCache(array $objects) {
    foreach ($objects as $object) {
      $id = $object->getId();
      // Save the objects in the static and regular cache.
      $this->staticCache[$id] = $object;
      $this->getCachePool()->getItem($id)->set($object);
      if (!empty($object->pid)) {
        $this->setPidMappingId($object->pid, $id);
      }
    }

    $this->savePidMapping();
  }

  /**
   * {@inheritdoc}
   */
  public function resetCache(array $ids = NULL) {
    if (!empty($ids)) {
      $this->staticCache = array_diff_key($this->staticCache, array_flip($ids));
      foreach ($ids as $id) {
        $this->getCachePool()->getItem($id)->clear();
      }

      $pidMapping = &$this->getPidMapping();
      $pidMapping = array_diff($pidMapping, $ids);
      $this->savePidMapping($pidMapping);

      $this->getLogger()->notice("Cleared cache for {count} {type} items ({ids}).", array(
        'count' => count($ids),
        'type' => $this->objectType,
        'ids' => implode(', ', $ids)
      ));
    }
    elseif (!isset($ids)) {
      $this->staticCache = array();
      $this->getCachePool()->flush();
      $this->savePidMapping(array());
      $this->getLogger()->notice("Cleared cache for all {type} items.", array('type' => $this->objectType));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function createObject(array $data) {
    return call_user_func(array($this->objectClass, 'create'), $data);
  }

  /**
   * {@inheritdoc}
   */
  public function createObjects(array $data) {
    $objects = array();
    foreach ($data as $object_data) {
      $object = $this->createObject($object_data);
      $objects[$object->getId()] = $object;
    }
    return $objects;
  }

  /**
   * {@inheritdoc}
   */
  public function getNotificationService() {
    if (!isset($this->notificationService)) {
      $uri = call_user_func(array($this->objectClass, 'getNotificationUri'));
      if (!$uri) {
        throw new NotificationsUnsupportedException("The " . $this->getObjectType() . " object does not support notifications.");
      }

      $uri = is_string($uri) ? new Uri($uri) : $uri;
      // Allow some query parameters to be reused from the object service's URI.
      $query = $this->getUri()->getQuery();
      parse_str($query, $query);
      /** @var array $query */
      foreach (array_keys($query) as $key) {
        if (!in_array($key, array('account'))) {
          $uri = $uri::withoutQueryValue($uri, $key);
        }
      }

      $this->notificationService = new ObjectNotificationService($uri, $this, $this->client, $this->cachePool, $this->logger);
    }
    return $this->notificationService;
  }

}
