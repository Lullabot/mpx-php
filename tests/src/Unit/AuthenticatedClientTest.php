<?php

namespace Lullabot\Mpx\Tests\Unit;

use Cache\Adapter\PHPArray\ArrayCachePool;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use Lullabot\Mpx\AuthenticatedClient;
use Lullabot\Mpx\Client;
use Lullabot\Mpx\DataService\Access\Account;
use Lullabot\Mpx\Service\IdentityManagement\User;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\Tests\JsonResponse;
use Lullabot\Mpx\Tests\MockClientTrait;
use Lullabot\Mpx\Token;
use Lullabot\Mpx\TokenCachePool;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\StoreInterface;

/**
 * @coversDefaultClass \Lullabot\Mpx\AuthenticatedClient
 */
class AuthenticatedClientTest extends TestCase
{
    use MockClientTrait;

    /**
     * Test normal authenticated requests.
     *
     * @dataProvider clientMethodDataProvider
     *
     * @param string $method The method on AuthenticatedClient to call.
     * @param array  $args   The method arguments.
     *
     * @covers ::__construct
     * @covers ::request
     * @covers ::requestAsync
     * @covers ::requestAsyncWithRetry
     * @covers ::send
     * @covers ::sendAsync
     * @covers ::sendAsyncWithRetry
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
        /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        $store->expects($this->any())
            ->method('exists')
            ->willReturn(false);
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        $logger = $this->fetchTokenLogger(1);

        $user = new User('mpx/USER-NAME', 'correct-password');
        $userSession = new UserSession($user, $client, $store, $tokenCachePool);
        $userSession->setLogger($logger);
        $authenticatedClient = new AuthenticatedClient($client, $userSession);
        $response = \call_user_func_array([$authenticatedClient, $method], $args);
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
     * @param string $method The method on AuthenticatedClient to call.
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
        /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        $store->expects($this->any())
            ->method('exists')
            ->willReturn(false);
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        $logger = $this->fetchTokenLogger(2);

        $user = new User('mpx/USER-NAME', 'correct-password');
        $userSession = new UserSession($user, $client, $store, $tokenCachePool);
        $userSession->setLogger($logger);
        $authenticatedClient = new AuthenticatedClient($client, $userSession);
        $response = \call_user_func_array([$authenticatedClient, $method], $args);
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
     * @param string $method The method on AuthenticatedClient to call.
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
        /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        $store->expects($this->any())
            ->method('exists')
            ->willReturn(false);
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        $logger = $this->fetchTokenLogger(1);

        $user = new User('mpx/USER-NAME', 'correct-password');
        $userSession = new UserSession($user, $client, $store, $tokenCachePool);
        $userSession->setLogger($logger);
        $authenticatedClient = new AuthenticatedClient($client, $userSession);
        $this->expectException(ClientException::class);
        $response = \call_user_func_array([$authenticatedClient, $method], $args);
        if ($response instanceof PromiseInterface) {
            $response->wait();
        }
    }

    /**
     * Test authenticated requests that should not retry due to a 5XX error.
     *
     * @dataProvider clientMethodDataProvider
     *
     * @param string $method The method on AuthenticatedClient to call.
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
        /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        $store->expects($this->any())
            ->method('exists')
            ->willReturn(false);
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        $logger = $this->fetchTokenLogger(1);

        $user = new User('mpx/USER-NAME', 'correct-password');
        $userSession = new UserSession($user, $client, $store, $tokenCachePool);
        $userSession->setLogger($logger);
        $authenticatedClient = new AuthenticatedClient($client, $userSession);
        $this->expectException(ServerException::class);
        $response = \call_user_func_array([$authenticatedClient, $method], $args);
        if ($response instanceof PromiseInterface) {
            $response->wait();
        }
    }

    /**
     * @covers ::getConfig
     */
    public function testGetConfig()
    {
        /** @var Client|\PHPUnit\Framework\MockObject\MockObject $client */
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();
        $client->expects($this->once())->method('getConfig')->with('the-option')
            ->willReturn('the-value');

        /** @var UserSession|\PHPUnit\Framework\MockObject\MockObject $userSession */
        $userSession = $this->getMockBuilder(UserSession::class)
            ->disableOriginalConstructor()
            ->getMock();
        $authenticatedClient = new AuthenticatedClient($client, $userSession);
        $this->assertEquals('the-value', $authenticatedClient->getConfig('the-option'));
    }

    /**
     * Test setting a duration for renewed tokens.
     *
     * @covers ::setTokenDuration
     * @covers ::mergeAuth
     */
    public function testTokenDuration()
    {
        /** @var Client|\PHPUnit\Framework\MockObject\MockObject $client */
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var UserSession|\PHPUnit\Framework\MockObject\MockObject $userSession */
        $userSession = $this->getMockBuilder(UserSession::class)
            ->disableOriginalConstructor()
            ->getMock();
        $authenticatedClient = new AuthenticatedClient($client, $userSession);
        $duration = rand(1, 3600);
        $userSession->expects($this->once())->method('acquireToken')
            ->with($duration, false)
            ->willReturn(new Token('mpx/USER-ID', 'abcdef', $duration));
        $authenticatedClient->setTokenDuration($duration);
        $authenticatedClient->request('GET', 'https://www.example.com/');
    }

    /**
     * @covers ::hasAccount
     * @covers ::getAccount
     */
    public function testGetAccount()
    {
        /** @var Client|\PHPUnit\Framework\MockObject\MockObject $client */
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var UserSession|\PHPUnit\Framework\MockObject\MockObject $userSession */
        $userSession = $this->getMockBuilder(UserSession::class)
            ->disableOriginalConstructor()
            ->getMock();

        $account = new Account();
        $authenticatedClient = new AuthenticatedClient($client, $userSession, $account);
        $this->assertTrue($authenticatedClient->hasAccount());

        $this->assertSame($account, $authenticatedClient->getAccount());
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
     * @return \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface
     */
    private function fetchTokenLogger(int $count)
    {
        /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $logger */
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
                    $this->assertEquals('Retrieved a new mpx token {token} for user {username} that expires on {date}.', $message);
                    $this->assertArraySubset([
                        'token' => 'TOKEN-VALUE',
                        'username' => 'mpx/USER-NAME',
                    ], $context);
                    $this->assertRegExp('!\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{4}!', $context['date']);
                });
        }

        return $logger;
    }
}
