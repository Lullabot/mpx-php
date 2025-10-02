<?php

namespace Lullabot\Mpx\Tests\Unit;

use Lullabot\Mpx\Cache\Adapter\PHPArray\ArrayCachePool;
use Lullabot\Mpx\Service\IdentityManagement\User;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\Token;
use Lullabot\Mpx\TokenCachePool;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Lullabot\Mpx\TokenCachePool
 */
class TokenCachePoolTest extends TestCase
{
    /**
     * @var \Lullabot\Mpx\Service\IdentityManagement\UserSession|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $user;

    /**
     * @var \Lullabot\Mpx\Token
     */
    protected $token;

    protected function setUp(): void
    {
        /* @var \Lullabot\Mpx\Service\IdentityManagement\UserSession|\PHPUnit_Framework_MockObject_MockObject $user */
        $this->user = $this->getMockBuilder(UserSession::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->user->method('getUser')->willReturn(new User('mpx/username', 'password'));
        $this->token = new Token('https://example.com/idm/data/User/mpx/123456', 'value', time() + 60);
    }

    /**
     * Test getting and setting the token from the cache.
     *
     * @covers ::__construct
     * @covers ::setToken
     * @covers ::getToken
     * @covers ::cacheKey
     */
    public function testGetSetToken()
    {
        $cache = new TokenCachePool(new ArrayCachePool());
        $cache->setToken($this->user, $this->token);
        $this->assertEquals($this->token, $cache->getToken($this->user));
    }

    /**
     * Test that an expired token is not returned.
     *
     * @covers ::setToken
     * @covers ::getToken
     */
    public function testExpiresToken()
    {
        $token = new Token('https://example.com/idm/data/User/mpx/123456', 'value', 1);
        $cache = new TokenCachePool(new ArrayCachePool());
        $cache->setToken($this->user, $token);
        sleep(1);
        $this->expectException(\RuntimeException::class);
        $cache->getToken($this->user);
    }

    /**
     * Test deleting a token.
     *
     * @covers ::deleteToken
     * @covers ::getToken
     * @covers ::cacheKey
     */
    public function testDeleteToken()
    {
        $cache = new TokenCachePool(new ArrayCachePool());
        $cache->setToken($this->user, $this->token);
        $cache->deleteToken($this->user);
        $this->expectException(\RuntimeException::class);
        $cache->getToken($this->user);
    }
}
