<?php

namespace Mpx;

use Psr\Log\LoggerInterface;
use Stash\Interfaces\PoolInterface;
use Stash\Driver\Apc;
use Stash\Driver\Ephemeral;
use Stash\Pool;

trait HasCachePoolTrait {

  /**
   * @var \Stash\Interfaces\PoolInterface
   */
  protected $cachePool;

  /**
   * Sets the cache pool.
   *
   * @param \Stash\Interfaces\PoolInterface $cachePool
   *
   * @return static
   */
  public function setCachePool(PoolInterface $cachePool) {
    $this->cachePool = $cachePool;
    return $this;
  }

  /**
   * Returns the current cache pool.
   *
   * @return \Stash\Interfaces\PoolInterface
   */
  public function getCachePool() {
    if (!isset($this->cachePool)) {
      $driver = Apc::isAvailable() ? new Apc() : new Ephemeral();
      $this->cachePool = new Pool($driver);
      if (isset($this->logger) && $this->logger instanceof LoggerInterface) {
        $this->cachePool->setLogger($this->logger);
      }
    }
    return $this->cachePool;
  }

}
