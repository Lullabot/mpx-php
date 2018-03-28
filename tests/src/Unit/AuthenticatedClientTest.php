<?php

namespace Lullabot\Mpx\Tests\Unit;

use Cache\Adapter\PHPArray\ArrayCachePool;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use Lullabot\Mpx\Service\IdentityManagement\AuthenticatedClient;
use Lullabot\Mpx\Tests\JsonResponse;
use Lullabot\Mpx\Tests\MockClientTrait;
use Lullabot\Mpx\TokenCachePool;
use Lullabot\Mpx\UserSession;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\StoreInterface;

/**
 * @coversDefaultClass \Lullabot\Mpx\Service\IdentityManagement\AuthenticatedClient
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
        /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        $logger = $this->fetchTokenLogger(1);

        $user = new UserSession($client, $store, $tokenCachePool, $logger, 'USER-NAME', 'correct-password');
        $session = new AuthenticatedClient($client, $user);
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
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        $logger = $this->fetchTokenLogger(2);

        $user = new UserSession($client, $store, $tokenCachePool, $logger, 'USER-NAME', 'correct-password');
        $session = new AuthenticatedClient($client, $user);
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
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        $logger = $this->fetchTokenLogger(1);

        $user = new UserSession($client, $store, $tokenCachePool, $logger, 'USER-NAME', 'correct-password');
        $session = new AuthenticatedClient($client, $user);
        $this->expectException(ClientException::class);
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
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        $logger = $this->fetchTokenLogger(1);

        $user = new UserSession($client, $store, $tokenCachePool, $logger, 'USER-NAME', 'correct-password');
        $session = new AuthenticatedClient($client, $user);
        $this->expectException(ServerException::class);
        $response = call_user_func_array([$session, $method], $args);
        if ($response instanceof PromiseInterface) {
            $response->wait();
        }
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
