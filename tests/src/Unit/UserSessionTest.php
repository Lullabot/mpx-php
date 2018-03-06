<?php

namespace Mpx\Tests\Unit;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Mpx\Exception\ApiException;
use Mpx\Tests\JsonResponse;
use Mpx\Tests\MockClientTrait;
use Mpx\TokenCachePool;
use Mpx\User;
use Mpx\UserSession;
use PHPUnit\Framework\TestCase;

/**
 * @class UserSessionTest
 * @package Mpx\Tests\Unit
 * @coversDefaultClass Mpx\UserSession
 */
class UserSessionTest extends TestCase {

  use MockClientTrait;

  public function testAcquireToken() {

  }

  /**
   * @covers ::signIn
   * @covers ::signOut
   */
  public function testSignIn() {
    $client = $this->getMockClient([
      new JsonResponse(200, [], 'signin-fail.json'),
    ]);
    $user = new User('USER-NAME', 'incorrect-password');
    $session = new UserSession($client, $user, new TokenCachePool(new ArrayCachePool()));
    $this->expectException(ApiException::class);
    $this->expectExceptionMessage('Error com.theplatform.authentication.api.exception.AuthenticationException on request to https://identity.auth.theplatform.com/idm/web/Authentication/signIn: Either \'USER-NAME\' does not have an account with this site, or the password was incorrect.');
    $this->expectExceptionCode(401);
    $session->acquireToken();
  }

  public function testSignOut() {

  }

}
