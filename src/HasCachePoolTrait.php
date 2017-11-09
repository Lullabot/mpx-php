<?php

namespace Mpx;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Psr\Cache\CacheItemPoolInterface;

trait HasCachePoolTrait {

  /**
   * @var \Psr\Cache\CacheItemPoolInterface
   */
  protected $cachePool;

  /**
   * Sets a cache pool.
   *
   * @param \Psr\Cache\CacheItemPoolInterface $cachePool
   *
   * @return static
   */
  public function setCachePool(CacheItemPoolInterface $cachePool) {
    $this->cachePool = $cachePool;
    return $this;
  }

  /**
   * @return \Psr\Cache\CacheItemPoolInterface
   */
  public function getCachePool() {
    if (!isset($this->cachePool)) {
      $this->cachePool = new ArrayCachePool();
    }
    return $this->cachePool;
  }

}
