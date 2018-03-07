<?php

namespace Lullabot\Mpx\Tests\Unit;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Lullabot\Mpx\Client;
use Lullabot\Mpx\Exception\ApiException;
use Lullabot\Mpx\Exception\ClientException;
use Lullabot\Mpx\Tests\JsonResponse;
use Lullabot\Mpx\Tests\MockClientTrait;
use Lullabot\Mpx\TokenCachePool;
use Lullabot\Mpx\User;
use Lullabot\Mpx\UserSession;
use PHPUnit\Framework\TestCase;

/**
 * @class UserSessionTest
 * @package Lullabot\Mpx\Tests\Unit
 * @coversDefaultClass Lullabot\Mpx\UserSession
 */
class UserSessionTest extends TestCase {

    use MockClientTrait;

    /**
     * @covers ::acquireToken
     * @covers ::signIn
     * @covers ::signOut
     */
    public function testAcquireToken() {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'signout.json'),
        ]);
        $user = new User('USER-NAME', 'correct-password');
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());
        $session = new UserSession($client, $user, $tokenCachePool);
        $token = $session->acquireToken();
        $this->assertEquals($token, $tokenCachePool->getToken($user));
        $session->signOut();
        $this->expectException(\RuntimeException::class);
        $tokenCachePool->getToken($user);
    }

    /**
     * @covers ::acquireToken
     * @covers ::signIn
     */
    public function testAcquireTokenFailure() {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-fail.json'),
        ]);
        $user = new User('USER-NAME', 'incorrect-password');
        $session = new UserSession($client, $user, new TokenCachePool(new ArrayCachePool()));
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage("Error com.theplatform.authentication.api.exception.AuthenticationException: Either 'USER-NAME' does not have an account with this site, or the password was incorrect.");
        $this->expectExceptionCode(401);
        $session->acquireToken();
    }

}
