<?php

namespace Lullabot\Mpx\Service\AccessManagement;

use Lullabot\Mpx\AuthenticatedClient;
use Lullabot\Mpx\Cache\Adapter\PHPArray\ArrayCachePool;
use Psr\Cache\CacheItemPoolInterface;

abstract class ResolveBase
{
    /**
     * The cache used to store resolveDomain responses.
     *
     * @var CacheItemPoolInterface
     */
    protected $cache;
    /**
     * The client used to access mpx.
     *
     * @var \Lullabot\Mpx\AuthenticatedClient
     */
    protected $authenticatedClient;

    /**
     * ResolveDomain constructor.
     *
     * @param AuthenticatedClient         $authenticatedClient The client used to access mpx.
     * @param CacheItemPoolInterface|null $cache               (optional) The cache to store responses in. Defaults to an array cache.
     */
    public function __construct(AuthenticatedClient $authenticatedClient, CacheItemPoolInterface $cache = null)
    {
        $this->authenticatedClient = $authenticatedClient;

        if (!$cache) {
            $cache = new ArrayCachePool();
        }
        $this->cache = $cache;
    }

    protected function saveCache($key, $resolved)
    {
        // thePlatform provides no guidance on how long we can cache this for.
        // Since many of their examples and other mpx clients hardcode these
        // values, we assume 30 days and that they will implement redirects or
        // domain aliases if required.
        $this->cache->set($key, $resolved, new \DateInterval('P30D'));
    }
}
