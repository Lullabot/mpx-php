<?php

namespace Lullabot\Mpx\Tests\Unit\Service\IdentityManagement;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Lullabot\Mpx\Cache\Adapter\PHPArray\ArrayCachePool;
use Lullabot\Mpx\Exception\ClientException;
use Lullabot\Mpx\Exception\TokenNotFoundException;
use Lullabot\Mpx\Service\IdentityManagement\ServiceTokenFlow;
use Lullabot\Mpx\Service\IdentityManagement\ServiceUser;
use Lullabot\Mpx\Service\IdentityManagement\SignInFlow;
use Lullabot\Mpx\Service\IdentityManagement\User;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\Tests\Fixtures\DummyStoreInterface;
use Lullabot\Mpx\Tests\JsonResponse;
use Lullabot\Mpx\Tests\MockClientTrait;
use Lullabot\Mpx\Token;
use Lullabot\Mpx\TokenCachePool;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Lock\Exception\LockConflictedException;

/**
 * Tests mpx user accounts.
 *
 * @coversDefaultClass \Lullabot\Mpx\Service\IdentityManagement\UserSession
 */
class UserSessionTest extends TestCase
{
    use ArraySubsetAsserts;
    use MockClientTrait;

    /**
     * @covers ::__construct
     * @covers ::acquireToken
     * @covers ::signIn
     * @covers ::signInWithLock
     * @covers ::signOut
     * @covers ::getFlow
     */
    public function testAcquireToken(): void
    {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'signout.json'),
        ]);
        /** @var DummyStoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(DummyStoreInterface::class)
            ->getMock();
        $store->expects($this->any())
            ->method('exists')
            ->willReturn(false);
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        $logger = $this->fetchTokenLogger(1);

        $user = new User('mpx/USER-NAME', 'correct-password');
        $userSession = new UserSession($user, $client, new SignInFlow(), $store, $tokenCachePool);
        $userSession->setLogger($logger);
        $token = $userSession->acquireToken();
        $this->assertEquals($token, $tokenCachePool->getToken($userSession));
        $userSession->signOut();
        $this->expectException(\RuntimeException::class);
        $tokenCachePool->getToken($userSession);
    }

    /**
     * @covers ::acquireToken
     * @covers ::signIn
     * @covers ::signInWithLock
     */
    public function testAcquireTokenFailure(): void
    {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-fail.json'),
        ]);

        /** @var DummyStoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(DummyStoreInterface::class)
            ->getMock();
        $store->expects($this->any())
            ->method('exists')
            ->willReturn(false);
        /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();

        $logger->expects($this->any())->method('info')
            ->withConsecutive(['Successfully acquired the "{resource}" lock.']);

        $user = new User('mpx/USER-NAME', 'incorrect-password');
        $userSession = new UserSession($user, $client, new SignInFlow(), $store, new TokenCachePool(new ArrayCachePool()));
        $userSession->setLogger($logger);
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage("Error com.theplatform.authentication.api.exception.AuthenticationException: Either 'mpx/USER-NAME' does not have an account with this site, or the password was incorrect.");
        $this->expectExceptionCode(401);
        $userSession->acquireToken();
    }

    /**
     * Test that resetting a token executes a new mpx request.
     *
     * @covers ::acquireToken
     */
    public function testAcquireReset(): void
    {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'signin-success.json'),
        ]);
        /** @var DummyStoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(DummyStoreInterface::class)
            ->getMock();
        $store->expects($this->any())
            ->method('exists')
            ->willReturn(false);
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $logger->expects($this->any())->method('debug')
            ->withConsecutive(['Successfully acquired the "{resource}" lock.'],
                ['Expiration defined for "{resource}" lock for "{ttl}" seconds.'],
                ['Successfully acquired the "{resource}" lock.'],
                ['Expiration defined for "{resource}" lock for "{ttl}" seconds.']);
        $logger->expects($this->any())->method('info')
            ->withConsecutive(['Retrieved a new mpx token {token} for user {username} that expires on {date}.'],
                ['Retrieved a new mpx token {token} for user {username} that expires on {date}.']);

        $user = new User('mpx/USER-NAME', 'correct-password');
        $userSession = new UserSession($user, $client, new SignInFlow(), $store, $tokenCachePool);
        $userSession->setLogger($logger);
        $first_token = $userSession->acquireToken();
        $this->assertEquals($first_token, $tokenCachePool->getToken($userSession));
        $second_token = $userSession->acquireToken(null, true);
        $this->assertEquals($second_token, $tokenCachePool->getToken($userSession));
        $this->assertNotSame($first_token, $second_token);
    }

    /**
     * Test that acquiring a token fails if the lock cannot be grabbed.
     *
     * @covers ::signInWithLock
     */
    public function testConcurrentSignInFails(): void
    {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
        ]);
        /** @var DummyStoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(DummyStoreInterface::class)
            ->getMock();
        $store->expects($this->once())->method('waitAndSave')
            ->willThrowException(new LockConflictedException());
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        // We cover logging in other tests.
        $logger = new NullLogger();

        $user = new User('mpx/USER-NAME', 'correct-password');
        $userSession = new UserSession($user, $client, new SignInFlow(), $store, $tokenCachePool);
        $userSession->setLogger($logger);
        $this->expectException(LockConflictedException::class);
        $userSession->acquireToken();
    }

    /**
     * Test that sessions sign in against the identity management service by default.
     *
     * @covers ::__construct
     * @covers ::getFlow
     */
    public function testDefaultFlow(): void
    {
        $session = new UserSession(new User('mpx/USER-NAME', 'correct-password'), $this->getMockClient(), new SignInFlow());

        $this->assertInstanceOf(SignInFlow::class, $session->getFlow());
    }

    /**
     * Test that an explicit flow overrides the default.
     *
     * @covers ::__construct
     * @covers ::getFlow
     */
    public function testExplicitFlow(): void
    {
        $flow = new ServiceTokenFlow();
        $session = new UserSession(new ServiceUser('CLIENT-ID', 'CLIENT-SECRET'), $this->getMockClient(), $flow);

        $this->assertSame($flow, $session->getFlow());
    }

    /**
     * Test that the two flows never share a cached token.
     *
     * @covers ::__construct
     */
    public function testFlowsDoNotShareCachedTokens(): void
    {
        $client = $this->getMockClient();
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        // Identical credentials, but authenticated two different ways.
        $signIn = new UserSession(new User('mpx/USER-NAME', 'password'), $client, new SignInFlow(), null, $tokenCachePool);
        $service = new UserSession(new ServiceUser('mpx/USER-NAME', 'password'), $client, new ServiceTokenFlow(), null, $tokenCachePool);

        $tokenCachePool->setToken($signIn, new Token('http://example.com/User/1', 'TOKEN-VALUE', 3600));

        $this->expectException(TokenNotFoundException::class);
        $tokenCachePool->getToken($service);
    }

    /**
     * Fetch a logger that expects a number of tokens to be logged.
     *
     * @param int $count The number of times a token is logged.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    private function fetchTokenLogger(int $count)
    {
        /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();

        for ($tokens = 0; $tokens < $count; ++$tokens) {
            // Since our class instantiates the Lock and passes in the logger, we have to expect these method calls
            // if we want to assert the last method call in this loop.
            $logger->expects($this->any())->method('info')
                ->with('Retrieved a new mpx token {token} for user {username} that expires on {date}.');
            $logger->expects($this->any())->method('debug')
                ->withConsecutive(['Successfully acquired the "{resource}" lock.'],
                    ['Expiration defined for "{resource}" lock for "{ttl}" seconds.']);
        }

        return $logger;
    }
}
