<?php

namespace Lullabot\Mpx\Tests\Unit;

use Cache\Adapter\PHPArray\ArrayCachePool;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use Lullabot\Mpx\Exception\ClientException;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\Tests\JsonResponse;
use Lullabot\Mpx\Tests\MockClientTrait;
use Lullabot\Mpx\TokenCachePool;
use Lullabot\Mpx\User;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Lock\Exception\LockConflictedException;
use Symfony\Component\Lock\StoreInterface;

/**
 * @class UserSessionTest
 * @coversDefaultClass \Lullabot\Mpx\Service\IdentityManagement\UserSession
 */
class UserSessionTest extends TestCase
{
    use MockClientTrait;

    /**
     * @covers ::__construct
     * @covers ::acquireToken
     * @covers ::signIn
     * @covers ::signInWithLock
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
        /** @var StoreInterface|\PHPUnit\Framework\MockObject\MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        $logger = $this->fetchTokenLogger(1);

        $session = new UserSession($client, $user, $store, $tokenCachePool, $logger);
        $token = $session->acquireToken();
        $this->assertEquals($token, $tokenCachePool->getToken($user));
        $session->signOut();
        $this->expectException(\RuntimeException::class);
        $tokenCachePool->getToken($user);
    }

    /**
     * @covers ::acquireToken
     * @covers ::signIn
     * @covers ::signInWithLock
     */
    public function testAcquireTokenFailure()
    {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-fail.json'),
        ]);
        $user = new User('USER-NAME', 'incorrect-password');

        /** @var StoreInterface|\PHPUnit\Framework\MockObject\MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        /** @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject $logger */
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();

        $logger->expects($this->at(0))->method('info')
            ->with('Successfully acquired the "{resource}" lock.');

        $session = new UserSession($client, $user, $store, new TokenCachePool(new ArrayCachePool()), $logger);
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
        /** @var StoreInterface|\PHPUnit\Framework\MockObject\MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        /** @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject $logger */
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $logger->expects($this->at(0))->method('info')
            ->with('Successfully acquired the "{resource}" lock.');
        $logger->expects($this->at(1))->method('info')
            ->with('Expiration defined for "{resource}" lock for "{ttl}" seconds.');
        $logger->expects($this->at(2))->method('info')
            ->willReturnCallback(function ($message, $context) {
                $this->assertEquals('Retrieved a new MPX token {token} for user {username} that expires on {date}.', $message);
                $this->assertArraySubset([
                    'token' => 'TOKEN-VALUE',
                    'username' => 'USER-NAME',
                ], $context);
                $this->assertRegExp('!\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{4}!', $context['date']);
            });
        $logger->expects($this->at(3))->method('info')
            ->with('Successfully acquired the "{resource}" lock.');
        $logger->expects($this->at(4))->method('info')
            ->with('Expiration defined for "{resource}" lock for "{ttl}" seconds.');
        $logger->expects($this->at(5))->method('info')
            ->willReturnCallback(function ($message, $context) {
                $this->assertEquals('Retrieved a new MPX token {token} for user {username} that expires on {date}.', $message);
                $this->assertArraySubset([
                    'token' => 'TOKEN-VALUE',
                    'username' => 'USER-NAME',
                ], $context);
                $this->assertRegExp('!\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{4}!', $context['date']);
            });

        $session = new UserSession($client, $user, $store, $tokenCachePool, $logger);
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
                $parts = \GuzzleHttp\Psr7\parse_query($request->getUri()->getQuery());
                $this->assertEquals('TOKEN-VALUE', $parts['token']);

                return new JsonResponse(200, [], 'getSelfId.json');
            },
        ]);
        $user = new User('USER-NAME', 'correct-password');
        /** @var StoreInterface|\PHPUnit\Framework\MockObject\MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        $logger = $this->fetchTokenLogger(1);

        $session = new UserSession($client, $user, $store, $tokenCachePool, $logger);
        $response = call_user_func_array([$session, $method], $args);
        if ($response instanceof PromiseInterface) {
            $response = $response->wait();
        }
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test retried authenticated requests.
     *
     * @dataProvider clientMethodDataProvider
     *
     * @param string $method The method on UserSession to call.
     * @param array  $args   The method arguments.
     *
     * @covers ::requestWithRetry
     * @covers ::requestAsyncWithRetry
     * @covers ::sendWithRetry
     * @covers ::sendAsyncWithRetry
     * @covers ::outerPromise
     * @covers ::finallyResolve
     * @covers ::isTokenAuthError
     */
    public function testRetriedAuthenticatedRequest(string $method, array $args)
    {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(401, [], 'invalid-token.json'),
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'getSelfId.json'),
        ]);
        $user = new User('USER-NAME', 'correct-password');
        /** @var StoreInterface|\PHPUnit\Framework\MockObject\MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        $logger = $this->fetchTokenLogger(2);

        $session = new UserSession($client, $user, $store, $tokenCachePool, $logger);
        $response = call_user_func_array([$session, $method], $args);
        if ($response instanceof PromiseInterface) {
            $response = $response->wait();
        }
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test authenticated requests that should not retry.
     *
     * @dataProvider clientMethodDataProvider
     *
     * @param string $method The method on UserSession to call.
     * @param array  $args   The method arguments.
     *
     * @covers ::requestWithRetry
     * @covers ::requestAsyncWithRetry
     * @covers ::sendWithRetry
     * @covers ::sendAsyncWithRetry
     * @covers ::outerPromise
     * @covers ::isTokenAuthError
     */
    public function testNotRetriedAuthenticatedRequest(string $method, array $args)
    {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(403, [], '{}'),
        ]);
        $user = new User('USER-NAME', 'correct-password');
        /** @var StoreInterface|\PHPUnit\Framework\MockObject\MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        $logger = $this->fetchTokenLogger(1);

        $session = new UserSession($client, $user, $store, $tokenCachePool, $logger);
        $this->expectException(\GuzzleHttp\Exception\ClientException::class);
        $response = call_user_func_array([$session, $method], $args);
        if ($response instanceof PromiseInterface) {
            $response->wait();
        }
    }

    /**
     * Test authenticated requests that should not retry due to a 5XX error.
     *
     * @dataProvider clientMethodDataProvider
     *
     * @param string $method The method on UserSession to call.
     * @param array  $args   The method arguments.
     *
     * @covers ::requestWithRetry
     * @covers ::requestAsyncWithRetry
     * @covers ::sendWithRetry
     * @covers ::sendAsyncWithRetry
     */
    public function testServerExceptionAuthenticatedRequest(string $method, array $args)
    {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(503, [], '{}'),
        ]);
        $user = new User('USER-NAME', 'correct-password');
        /** @var StoreInterface|\PHPUnit\Framework\MockObject\MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        $logger = $this->fetchTokenLogger(1);

        $session = new UserSession($client, $user, $store, $tokenCachePool, $logger);
        $this->expectException(ServerException::class);
        $response = call_user_func_array([$session, $method], $args);
        if ($response instanceof PromiseInterface) {
            $response->wait();
        }
    }

    /**
     * Test that acquiring a token fails if the lock cannot be grabbed.
     *
     * @covers ::signInWithLock
     */
    public function testConcurrentSignInFails()
    {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
        ]);
        $user = new User('USER-NAME', 'correct-password');
        /** @var StoreInterface|\PHPUnit\Framework\MockObject\MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        $store->expects($this->once())->method('waitAndSave')
            ->willThrowException(new LockConflictedException());
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        // We cover logging in other tests.
        $logger = new NullLogger();

        $session = new UserSession($client, $user, $store, $tokenCachePool, $logger);
        $this->expectException(LockConflictedException::class);
        $session->acquireToken();
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

    /**
     * Fetch a logger that expects a number of tokens to be logged.
     *
     * @param int $count The number of times a token is logged.
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\Psr\Log\LoggerInterface
     */
    private function fetchTokenLogger(int $count)
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();

        $call = 0;
        for ($tokens = 0; $tokens < $count; ++$tokens) {
            // Since our class instantiates the Lock and passes in the logger, we have to expect these method calls
            // if we want to assert the last method call in this loop.
            $logger->expects($this->at($call++))->method('info')
                ->with('Successfully acquired the "{resource}" lock.');
            $logger->expects($this->at($call++))->method('info')
                ->with('Expiration defined for "{resource}" lock for "{ttl}" seconds.');

            $logger->expects($this->at($call++))->method('info')
                ->willReturnCallback(function ($message, $context) {
                    $this->assertEquals('Retrieved a new MPX token {token} for user {username} that expires on {date}.', $message);
                    $this->assertArraySubset([
                        'token' => 'TOKEN-VALUE',
                        'username' => 'USER-NAME',
                    ], $context);
                    $this->assertRegExp('!\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{4}!', $context['date']);
                });
        }

        return $logger;
    }
}
