<?php

namespace Mpx\Service;

use Mpx\CacheTrait;
use Mpx\ClientTrait;
use Mpx\ClientInterface;
use Mpx\LoggerTrait;
use Mpx\Object;
use Mpx\UserInterface;
use Pimple\Container;
use Psr\Log\LoggerInterface;
use Stash\Interfaces\PoolInterface;

abstract class AbstractObjectService implements ObjectServiceInterface {
  use CacheTrait;
  use ClientTrait;
  use LoggerTrait;

  /** @var \Mpx\UserInterface */
  protected $user;

  /** @var \GuzzleHttp\Url */
  protected $uri;

  /** @var array */
  protected $staticCache = array();

  /**
   * Construct an mpx object service.
   *
   * @param \Mpx\UserInterface $user
   * @param \Mpx\ClientInterface $client
   * @param \Stash\Interfaces\PoolInterface $cache
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function __construct(UserInterface $user, ClientInterface $client = NULL, PoolInterface $cache = NULL, LoggerInterface $logger = NULL) {
    $this->user = $user;
    $this->client = $client;
    // Prefix this cache pool namespace by object type.
    $this->cachePool = clone $cache;
    $this->cachePool->setNamespace($this->cachePool->getNamespace() . $this->getObjectType());
    $this->logger = $logger;
  }

  /**
   * Create a new instance of an object service class.
   *
   * @param \Mpx\UserInterface $user
   * @param \Pimple\Container $container
   *
   * @return static
   */
  public static function create(UserInterface $user, Container $container) {
    return new static(
      $user,
      $container['client'],
      $container['cache'],
      $container['logger']
    );
  }

  public function getUser() {
    return $this->user;
  }

  public function getUri() {
    return $this->uri;
  }

  abstract public function getSchema();

  abstract public function getObjectType();

  public function generateUri($path = '', $readOnly = TRUE) {
    $uri = clone $this->uri;
    if ($readOnly && strpos($uri->getHost(), 'read.') !== 0) {
      $uri->setHost('read.' . $uri->getHost());
    }
    if ($path) {
      $uri->addPath($path);
    }
    return $uri;
  }

  public function load($id) {
    $objects = $this->loadMultiple(array($id));
    if (!isset($objects[$id])) {
      throw new \Exception("Cannot load mpx {$this->getObjectType()} {$id} for {$this->getUser()->getUsername()}.");
    }
    return $objects[$id];
  }

  /**
   * Load multiple mpx objects.
   *
   * @param array $ids
   *   An array of IDs to load.
   *
   * @return array
   *   An array of mpx objects, indexed by ID.
   *
   * @throws \Exception
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
          $data = $this->client()->authenticatedGet(
            $this->getUser(),
            $this->generateUri('/data/' . $this->getObjectType() . '/' . implode(',', $batch), TRUE),
            [
              'query' => [
                'form' => 'cjson',
                'schema' => $this->getSchema(),
              ]
            ]
          );

          $data = isset($data['entries']) ? $data['entries'] : array($data);

          $objects = $this->createObjects($data);
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

  public function resetCache(array $ids = NULL) {
    if (!empty($ids)) {
      $this->staticCache = array_diff_key($this->staticCache, array_flip($ids));
      foreach ($ids as $id) {
        $this->cache()->getItem($id)->clear();
      }
      $this->logger()->info("Invalidated cache for {type} {ids}.", array(
        'type' => $this->getObjectType(),
        'ids' => implode(', ', $ids)
      ));
    }
    elseif (!isset($ids)) {
      $this->staticCache = array();
      $this->cache()->flush();
      $this->logger()->info("Cleared cache for all {type}.", array('type' => $this->getObjectType()));
    }
  }

  public function createObjects(array $data) {
    $objects = array();
    foreach ($data as $object_data) {
      $object = Object::create($object_data);
      // Normalize the ID value to just the actual ID and not the full URL.
      $objects[basename($object->id)] = $object;
    }
    //module_invoke_all('mpx_' . $this->type . '_load', $objects);
    return $objects;
  }

}
