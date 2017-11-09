<?php

namespace Mpx\Tests\Unit;

use Mpx\Exception\ApiException;
use Mpx\Tests\MockClientTrait;
use Mpx\User;
use PHPUnit\Framework\TestCase;
use Mpx\Tests\JsonResponse;

/**
 * @coversDefaultClass \Mpx\User
 */
class UserTest extends TestCase {

    use MockClientTrait;

    /**
     * @covers ::signIn
     */
    public function testSignInFailure() {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-fail.json'),
        ]);
        $user = new User('USER-NAME', 'incorrect-password', $client);
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Error com.theplatform.authentication.api.exception.AuthenticationException on request to https://identity.auth.theplatform.com/idm/web/Authentication/signIn: Either \'USER-NAME\' does not have an account with this site, or the password was incorrect.');
        $this->expectExceptionCode(401);
        $user->signIn();
    }

    /**
     * @covers ::signIn
     * @covers ::signOut
     * @covers ::setToken
     * @covers ::invalidateToken
     */
    public function testSignIn() {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'signout.json'),
        ]);
        $user = new User('USER-NAME', 'correct-password', $client);
        $user->signIn();
        $user->signOut();
    }

    /**
     * @covers ::signIn
     * @covers ::getId
     * @covers ::getSelfId
     * @covers ::acquireToken
     */
    public function testGetID() {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'getSelfId.json'),
        ]);
        $user = new User('USER-NAME', 'correct-password', $client);
        $user->signIn();
        $this->assertSame('1', $user->getId());
    }

}
