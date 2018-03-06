<?php

namespace Mpx\Tests\Unit;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Mpx\Token;
use Mpx\TokenCachePool;
use Mpx\User;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Mpx\TokenCachePool
 */
class TokenCachePoolTest extends TestCase {

    /**
     * @var \Mpx\User
     */
    protected $user;

    /**
     * @var \Mpx\Token
     */
    protected $token;

    /**
     * {@inheritdoc}
     */
    public function setUp() {
        $this->user = new User('username', 'password');
        $this->token = new Token('value', time() + 60);
    }

    /**
     * Test getting and setting the token from the cache.
     *
     * @covers ::setToken
     * @covers ::getToken
     */
    public function testGetSetToken() {
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
    public function testExpiresToken() {
        $this->markTestIncomplete('Expiration is not stored correctly on Tokens');
        $token = new Token('value', time() - 60);
        $cache = new TokenCachePool(new ArrayCachePool());
        $cache->setToken($this->user, $token);
        $this->expectException(\RuntimeException::class);
        $cache->getToken($this->user);
    }

    /**
     * Test deleting a token.
     *
     * @covers ::deleteToken
     * @covers ::getToken
     */
    public function testDeleteToken() {
        $cache = new TokenCachePool(new ArrayCachePool());
        $cache->setToken($this->user, $this->token);
        $cache->deleteToken($this->user);
        $this->expectException(\RuntimeException::class);
        $cache->getToken($this->user);
    }
}
