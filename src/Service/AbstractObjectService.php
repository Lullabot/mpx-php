<?php

namespace Mpx\Service;

use Mpx\CacheTrait;
use Mpx\ClientTrait;
use Mpx\ClientInterface;
use Mpx\LoggerTrait;
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
    $item = $this->cache()->getItem($id);
    $object = $item->get();

    if ($item->isMiss()) {
      $object = $this->client()->authenticatedGet(
        $this->user,
        $this->generateUri('/data/' . $this->getObjectType() . '/' . $id, TRUE),
        [
          'query' => [
            'form' => 'cjson',
            'schema' => $this->getSchema(),
          ]
        ]
      );
      $item->set($object);
      $this->logger()->info("Fetched {type} {id} from the API.", array('type' => $this->getObjectType(), 'id' => $id));
    }
    else {
      $this->logger()->info("Loaded {type} {id} from cache.", array('type' => $this->getObjectType(), 'id' => $id));
    }

    return $object;
  }

}
