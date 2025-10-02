<?php

namespace Lullabot\Mpx\Tests\Unit\Service\AccessManagement;

use Lullabot\Mpx\AuthenticatedClient;
use Lullabot\Mpx\Cache\Adapter\PHPArray\ArrayCachePool;
use Lullabot\Mpx\Service\AccessManagement\ResolveAllUrls;
use Lullabot\Mpx\Service\AccessManagement\ResolveAllUrlsResponse;
use Lullabot\Mpx\Service\IdentityManagement\User;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\Tests\Fixtures\DummyStoreInterface;
use Lullabot\Mpx\Tests\JsonResponse;
use Lullabot\Mpx\Tests\MockClientTrait;
use Lullabot\Mpx\TokenCachePool;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Test resolving all URLs for a given MPX service.
 *
 * @coversDefaultClass \Lullabot\Mpx\Service\AccessManagement\ResolveAllUrls
 */
class ResolveAllUrlsTest extends TestCase
{
    use MockClientTrait;

    /**
     * Test basic response loading.
     *
     * @covers ::resolve
     * @covers ::cacheKey
     * @covers \Lullabot\Mpx\Service\AccessManagement\ResolveBase::saveCache
     */
    public function testLoad()
    {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'resolveAllUrls.json'),
        ]);
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());
        /** @var DummyStoreInterface|\PHPUnit\Framework\MockObject\MockObject $store */
        $store = $this->getMockBuilder(DummyStoreInterface::class)
            ->getMock();
        $store->expects($this->any())
            ->method('exists')
            ->willReturn(false);
        $user = new User('mpx/USER-NAME', 'correct-password');
        $userSession = new UserSession($user, $client, $store, $tokenCachePool);
        $session = new AuthenticatedClient($client, $userSession);

        $cache = $this->getMockBuilder(ArrayCachePool::class)
            ->getMock();
        $item = $this->getMockBuilder(CacheItemInterface::class)
            ->getMock();
        $item->method('isHit')
            ->willReturn(false);

        $cache->method('getItem')
            ->with('6b590e46fe8d31b3d8cc0aa9c7282c4f')
            ->willReturn($item);

        $cache->expects($this->once())->method('set');

        $resolver = new ResolveAllUrls($session, $cache);
        $r = $resolver->resolve('Media Data Service');
        $this->assertEquals('Media Data Service', $r->getService());
        $this->assertEquals('https://data.media.theplatform.com/media', (string) $r->getUrl());
    }

    /**
     * Test cached response loading.
     *
     * @covers ::resolve
     * @covers ::cacheKey
     */
    public function testLoadCached()
    {
        $client = $this->getMockClient();
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());
        /** @var DummyStoreInterface $store */
        $store = $this->getMockBuilder(DummyStoreInterface::class)
            ->getMock();

        $user = new User('mpx/USER-NAME', 'correct-password');
        $userSession = new UserSession($user, $client, $store, $tokenCachePool);
        $session = new AuthenticatedClient($client, $userSession);

        $cache = $this->getMockBuilder(CacheItemPoolInterface::class)
            ->getMock();
        $item = $this->getMockBuilder(CacheItemInterface::class)
            ->getMock();
        $item->expects($this->once())->method('isHit')
            ->willReturn(true);
        $response = new ResolveAllUrlsResponse();
        $item->expects($this->once())->method('get')
            ->willReturn($response);

        $cache->method('getItem')
            ->with('6b590e46fe8d31b3d8cc0aa9c7282c4f')
            ->willReturn($item);

        $resolver = new ResolveAllUrls($session, $cache);
        $this->assertEquals($response, $resolver->resolve('Media Data Service'));
    }
}
