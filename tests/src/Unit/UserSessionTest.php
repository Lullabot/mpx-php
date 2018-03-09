<?php

namespace Lullabot\Mpx\Tests\Unit;

use Cache\Adapter\PHPArray\ArrayCachePool;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use Lullabot\Mpx\Exception\ClientException;
use Lullabot\Mpx\Tests\JsonResponse;
use Lullabot\Mpx\Tests\MockClientTrait;
use Lullabot\Mpx\TokenCachePool;
use Lullabot\Mpx\User;
use Lullabot\Mpx\UserSession;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

/**
 * @class UserSessionTest
 * @coversDefaultClass \Lullabot\Mpx\UserSession
 */
class UserSessionTest extends TestCase
{
    use MockClientTrait;

    /**
     * @covers ::__construct
     * @covers ::acquireToken
     * @covers ::signIn
     * @covers ::tokenFromResponse
     * @covers ::signOut
     */
    public function testAcquireToken()
    {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'signout.json'),
        ]);
        $user = new User('USER-NAME', 'correct-password');
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        /** @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject $logger */
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $logger->expects($this->once())->method('info')
            ->with(
                'Retrieved a new MPX token {token} for user {username} that expires on {date}.'
            )->willReturnCallback(function ($message, $context) {
                $this->assertEquals('Retrieved a new MPX token {token} for user {username} that expires on {date}.', $message);
                $this->assertArraySubset([
                    'token' => 'TOKEN-VALUE',
                    'username' => 'USER-NAME',
                ], $context);
                $this->assertRegExp('!\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{4}!', $context['date']);
            });

        $session = new UserSession($client, $user, $tokenCachePool, $logger);
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
    public function testAcquireTokenFailure()
    {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-fail.json'),
        ]);
        $user = new User('USER-NAME', 'incorrect-password');

        /** @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject $logger */
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $logger->expects($this->never())->method('info');

        $session = new UserSession($client, $user, new TokenCachePool(new ArrayCachePool()), $logger);
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage("Error com.theplatform.authentication.api.exception.AuthenticationException: Either 'USER-NAME' does not have an account with this site, or the password was incorrect.");
        $this->expectExceptionCode(401);
        $session->acquireToken();
    }

    /**
     * Test that resetting a token executes a new MPX request.
     *
     * @covers ::acquireToken
     */
    public function testAcquireReset()
    {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'signin-success.json'),
        ]);
        $user = new User('USER-NAME', 'correct-password');
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        /** @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject $logger */
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $logger->expects($this->exactly(2))->method('info')
            ->with(
                'Retrieved a new MPX token {token} for user {username} that expires on {date}.'
            )->willReturnCallback(function ($message, $context) {
                $this->assertEquals('Retrieved a new MPX token {token} for user {username} that expires on {date}.', $message);
                $this->assertArraySubset([
                    'token' => 'TOKEN-VALUE',
                    'username' => 'USER-NAME',
                ], $context);
                $this->assertRegExp('!\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{4}!', $context['date']);
            });

        $session = new UserSession($client, $user, $tokenCachePool, $logger);
        $first_token = $session->acquireToken();
        $this->assertEquals($first_token, $tokenCachePool->getToken($user));
        $second_token = $session->acquireToken(null, true);
        $this->assertEquals($second_token, $tokenCachePool->getToken($user));
        $this->assertNotSame($first_token, $second_token);
    }

    /**
     * Test normal authenticated requests.
     *
     * @dataProvider clientMethodDataProvider
     *
     * @param string $method The method on UserSession to call.
     * @param array  $args   The method arguments.
     *
     * @covers ::request
     * @covers ::requestAsync
     * @covers ::send
     * @covers ::sendAsync
     * @covers ::mergeAuth
     * @covers ::requestWithRetry
     * @covers ::sendWithRetry
     */
    public function testAuthenticatedRequest(string $method, array $args)
    {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            function (RequestInterface $request) {
                //  This is what tests mergeAuth().
                $this->assertEquals('Basic VVNFUi1OQU1FOlRPS0VOLVZBTFVF', $request->getHeaderLine('Authorization'));

                return new JsonResponse(200, [], 'getSelfId.json');
            },
        ]);
        $user = new User('USER-NAME', 'correct-password');
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        /** @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject $logger */
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $logger->expects($this->exactly(1))->method('info')
            ->with(
                'Retrieved a new MPX token {token} for user {username} that expires on {date}.'
            )->willReturnCallback(function ($message, $context) {
                $this->assertEquals('Retrieved a new MPX token {token} for user {username} that expires on {date}.', $message);
                $this->assertArraySubset([
                    'token' => 'TOKEN-VALUE',
                    'username' => 'USER-NAME',
                ], $context);
                $this->assertRegExp('!\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{4}!', $context['date']);
            });

        $session = new UserSession($client, $user, $tokenCachePool, $logger);
        $response = call_user_func_array([$session, $method], $args);
        if ($response instanceof PromiseInterface) {
            $response = $response->wait();
        }
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Return an array of methods and parameters to call for authenticated requests.
     *
     * @return array
     */
    public function clientMethodDataProvider()
    {
        return [
            ['request', ['GET', 'https://identity.auth.theplatform.com/idm/web/Self/getSelfId']],
            ['requestAsync', ['GET', 'https://identity.auth.theplatform.com/idm/web/Self/getSelfId']],
            ['send', [new Request('GET', 'https://identity.auth.theplatform.com/idm/web/Self/getSelfId')]],
            ['sendAsync', [new Request('GET', 'https://identity.auth.theplatform.com/idm/web/Self/getSelfId')]],
        ];
    }
}
