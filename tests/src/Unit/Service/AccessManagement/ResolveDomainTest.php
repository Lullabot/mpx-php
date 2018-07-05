<?php

namespace Lullabot\Mpx\Tests\Unit\Service\AccessManagement;

use Cache\Adapter\PHPArray\ArrayCachePool;
use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\AuthenticatedClient;
use Lullabot\Mpx\DataService\Access\Account;
use Lullabot\Mpx\Service\AccessManagement\ResolveDomain;
use Lullabot\Mpx\Service\AccessManagement\ResolveDomainResponse;
use Lullabot\Mpx\Service\IdentityManagement\User;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\Tests\JsonResponse;
use Lullabot\Mpx\Tests\MockClientTrait;
use Lullabot\Mpx\TokenCachePool;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Lock\StoreInterface;

/**
 * Tests resolving mpx domains and services.
 *
 * @coversDefaultClass  \Lullabot\Mpx\Service\AccessManagement\ResolveDomain
 */
class ResolveDomainTest extends TestCase
{
    use MockClientTrait;

    /**
     * Tests basic resolution.
     *
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testResolve()
    {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'resolveDomain.json'),
        ]);
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());
        /** @var StoreInterface $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();

        $user = new User('mpx/USER-NAME', 'correct-password');
        $userSession = new UserSession($user, $client, $store, $tokenCachePool);
        $authenticatedClient = new AuthenticatedClient($client, $userSession);
        $account = new Account();
        $account->setId(new Uri('http://example.com/1'));

        $resolveDomain = new ResolveDomain($authenticatedClient);
        $resolved = $resolveDomain->resolve($account);
        $this->assertInstanceOf(ResolveDomainResponse::class, $resolved);
        $this->assertNotEmpty($resolved->getServices());
    }

    /**
     * Tests cache hits and misses.
     *
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testResolveCache()
    {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'resolveDomain.json'),
        ]);
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());
        /** @var StoreInterface $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();

        $user = new User('mpx/USER-NAME', 'correct-password');
        $userSession = new UserSession($user, $client, $store, $tokenCachePool);
        $authenticatedClient = new AuthenticatedClient($client, $userSession);
        $account = new Account();
        $account->setId(new Uri('http://example.com/1'));

        $item = $this->getMockBuilder(CacheItemInterface::class)
            ->getMock();
        $cache = $this->getMockBuilder(CacheItemPoolInterface::class)
            ->getMock();
        $cache->expects($this->exactly(2))->method('getItem')
            ->willReturn($item);

        $cache->expects($this->once())->method('save');

        $item->expects($this->exactly(2))->method('isHit')
            ->willReturnOnConsecutiveCalls(false, true);
        $item->expects($this->at(0))->method('set');

        $resolveDomain = new ResolveDomain($authenticatedClient, $cache);

        // The cache miss.
        $response = $resolveDomain->resolve($account);

        $item->expects($this->once())->method('get')
            ->willReturn($response);

        // The cache hit.
        $this->assertEquals($response, $resolveDomain->resolve($account));
    }
}
