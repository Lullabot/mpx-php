<?php

namespace Lullabot\Mpx\Cache\Adapter\PHPArray;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * Array cache pool.
 *
 * You could set a limit of how many items you want to be stored to avoid memory
 * leaks.
 */
class ArrayCachePool implements LoggerAwareInterface, CacheInterface, CacheItemPoolInterface
{
    public const HIERARCHY_SEPARATOR = '|';

    protected ArrayAdapter $cache;

    public function __construct(int $limit = 0)
    {
        $this->cache = new ArrayAdapter(0, true, 0, $limit);
    }

    public function get($key, $default = null): mixed
    {
        $this->cache->getItem($key)->get();
    }

    public function set($key, $value, $ttl = null): bool
    {
        $item = $this->cache->getItem($key);
        $item->expiresAfter($ttl);

        return $this->cache->save($item);
    }

    public function delete($key): bool
    {
        $this->cache->delete($key);
    }

    public function getMultiple($keys, $default = null): iterable
    {
        return array_map(
            function (string $key) use ($default) {
                $item = $this->cache->getItem($key);

                return $item->isHit() ? $item->get() : $default;
            }, $keys);
    }

    public function setMultiple($values, $ttl = null): bool
    {
        $success = true;
        foreach ($values as $key => $value) {
            $item = $this->cache->getItem($key);
            $item->expiresAfter($ttl);
            $item->set($value);
            $success = $this->cache->save($item) && $success;
        }

        return $success;
    }

    public function deleteMultiple($keys): bool
    {
        return $this->deleteItems($keys);
    }

    public function has($key): bool
    {
        return $this->hasItem($key);
    }

    public function hasItem($key): bool
    {
        return $this->cache->hasItem($key);
    }

    public function clear(): bool
    {
        return $this->cache->clear();
    }

    public function deleteItem($key): bool
    {
        return $this->cache->deleteItem($key);
    }

    public function deleteItems(array $keys): bool
    {
        return $this->cache->deleteItems($keys);
    }

    public function save(CacheItemInterface $item): bool
    {
        return $this->cache->save($item);
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        return $this->cache->saveDeferred($item);
    }

    public function commit(): bool
    {
        return $this->cache->commit();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->cache->setLogger($logger);
    }

    public function invalidateTag($tag)
    {
        // We do not handle cache tags.
        return true;
    }

    public function invalidateTags(array $tags)
    {
        // We do not handle cache tags.
        return true;
    }

    public function getItem($key): CacheItemInterface
    {
        return $this->cache->getItem($key);
    }

    public function getItems(array $keys = []): iterable
    {
        return $this->cache->getItems($keys);
    }
}
