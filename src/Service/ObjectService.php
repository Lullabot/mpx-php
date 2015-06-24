<?php

namespace Mpx\Service;

use Mpx\CacheTrait;
use Mpx\ClientTrait;
use Mpx\ClientInterface;
use Mpx\Event\ObjectLoadEvent;
use Mpx\LoggerTrait;
use Mpx\Object;
use Mpx\UserInterface;
use Pimple\Container;
use Psr\Log\LoggerInterface;
use Stash\Interfaces\PoolInterface;
use GuzzleHttp\Event\HasEmitterTrait;
use GuzzleHttp\Url;

class ObjectService implements ObjectServiceInterface {
  use CacheTrait;
  use ClientTrait;
  use LoggerTrait;
  use HasEmitterTrait;

  /** @var \Mpx\UserInterface */
  protected $user;

  /** @var \GuzzleHttp\Url */
  protected $uri;

  /** @var array */
  protected $staticCache = array();

  /** @var string */
  protected $objectClass;

  /** @var string */
  protected $objectType;

  /** @var string */
  protected $schema;

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
    $uri = call_user_func(array($objectClass, 'getUri'));
    $this->uri = is_string($uri) ? Url::fromString($uri) : $uri;
    $this->schema = call_user_func(array($objectClass, 'getSchema'));

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
  public function getSchema() {
    return $this->schema;
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
  public function generateUri($path = '') {
    $uri = clone $this->uri;
    if ($path) {
      $uri->addPath($path);
    }
    return $uri;
  }

  /**
   * {@inheritdoc}
   */
  public function load($id) {
    $objects = $this->loadMultiple(array($id));
    if (!isset($objects[$id])) {
      throw new \Exception("Cannot load mpx {$this->objectType} {$id} for {$this->getUser()->getUsername()}.");
    }
    return $objects[$id];
  }

  /**
   * {@inheritdoc}
   */
  public function loadMultiple(array $ids) {
    if ($ids_to_load = array_diff($ids, array_keys($this->staticCache))) {
      foreach ($ids_to_load as $id) {
        $this->staticCache[$id] = $this->cache()->getItem($id)->get();
      }
      $ids_to_load = array_diff($ids_to_load, array_keys(array_filter($this->staticCache)));
      if ($ids_to_load) {
        // Ensure we are not trying to fetch too many items at once.
        $batches = array_chunk($ids_to_load, 50);
        foreach ($batches as $batch) {
          $data = $this->request('GET', '/data/' . $this->objectType . '/' . implode(',', $batch));
          // Normalize the data structure if only one result was returned.
          $data = isset($data['entries']) ? $data['entries'] : array($data);

          // Convert the data to objects.
          $objects = $this->createObjects($data);

          // Allow subscribers to be able to alter the objects before saving
          // them to the cache.
          $this->getEmitter()->emit('load', new ObjectLoadEvent($objects));

          // Save the objects in the static and regular cache.
          foreach ($objects as $id => $object) {
            $this->staticCache[$id] = $object;
            $this->cache()->getItem($id)->set($object);
          }
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
  public function request($method = 'GET', $path, array $options = []) {
    $uri = is_string($path) ? $this->generateUri($path) : $path;

    $options += array('query' => array());
    $options['query'] += array(
      'form' => 'cjson',
      'schema' => $this->schema,
    );

    return $this->client()->authenticatedRequest(
      $method,
      $uri,
      $this->getUser(),
      $options
    );
  }

  /**
   * {@inheritdoc}
   */
  public function resetCache(array $ids = NULL) {
    if (!empty($ids)) {
      $this->staticCache = array_diff_key($this->staticCache, array_flip($ids));
      foreach ($ids as $id) {
        $this->cache()->getItem($id)->clear();
      }
      $this->logger()->info("Cleared cache for {count} {type} items ({ids}).", array(
        'count' => count($ids),
        'type' => $this->objectType,
        'ids' => implode(', ', $ids)
      ));
    }
    elseif (!isset($ids)) {
      $this->staticCache = array();
      $this->cache()->flush();
      $this->logger()->info("Cleared cache for all {type} items.", array('type' => $this->objectType));
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

}
