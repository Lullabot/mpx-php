<?php

namespace Lullabot\Mpx\Tests\Unit\Service\IdentityManagement;

use Cache\Adapter\PHPArray\ArrayCachePool;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Lullabot\Mpx\Exception\ClientException;
use Lullabot\Mpx\Service\IdentityManagement\User;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\Tests\Fixtures\DummyStoreInterface;
use Lullabot\Mpx\Tests\JsonResponse;
use Lullabot\Mpx\Tests\MockClientTrait;
use Lullabot\Mpx\TokenCachePool;
use PHPUnit\Framework\ExpectationFailedException;
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
     * @covers ::signInOptions
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
        /** @var DummyStoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(DummyStoreInterface::class)
            ->getMock();
        $store->expects($this->any())
            ->method('exists')
            ->willReturn(false);
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        $logger = $this->fetchTokenLogger(1);

        $user = new User('mpx/USER-NAME', 'correct-password');
        $userSession = new UserSession($user, $client, $store, $tokenCachePool);
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
    public function testAcquireTokenFailure()
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
        /** @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();

        $logger->expects($this->any())->method('info')
            ->withConsecutive(['Successfully acquired the "{resource}" lock.']);

        $user = new User('mpx/USER-NAME', 'incorrect-password');
        $userSession = new UserSession($user, $client, $store, new TokenCachePool(new ArrayCachePool()));
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
    public function testAcquireReset()
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
        $logger->expects($this->any())->method('info')
            ->withConsecutive(['Successfully acquired the "{resource}" lock.'],
                ['Expiration defined for "{resource}" lock for "{ttl}" seconds.'],
                ['Retrieved a new mpx token {token} for user {username} that expires on {date}.', $this->callback(function ($context) {
                    try {
                        $this->assertArraySubset([
                            'token' => 'TOKEN-VALUE',
                            'username' => 'mpx/USER-NAME',
                        ], $context);
                        $this->assertMatchesRegularExpression('!\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{4}!', $context['date']);
                    } catch (ExpectationFailedException) {
                        return false;
                    }

                    return true;
                })],
                ['Successfully acquired the "{resource}" lock.'],
                ['Expiration defined for "{resource}" lock for "{ttl}" seconds.'],
                ['Retrieved a new mpx token {token} for user {username} that expires on {date}.', $this->callback(function ($context) {
                    try {
                        $this->assertArraySubset([
                            'token' => 'TOKEN-VALUE',
                            'username' => 'mpx/USER-NAME',
                        ], $context);
                        $this->assertMatchesRegularExpression('!\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{4}!', $context['date']);
                    } catch (ExpectationFailedException) {
                        return false;
                    }

                    return true;
                })]
            );

        $user = new User('mpx/USER-NAME', 'correct-password');
        $userSession = new UserSession($user, $client, $store, $tokenCachePool);
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
    public function testConcurrentSignInFails()
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
        $userSession = new UserSession($user, $client, $store, $tokenCachePool);
        $userSession->setLogger($logger);
        $this->expectException(LockConflictedException::class);
        $userSession->acquireToken();
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

        for ($tokens = 0; $tokens < $count; ++$tokens) {
            // Since our class instantiates the Lock and passes in the logger, we have to expect these method calls
            // if we want to assert the last method call in this loop.
            $logger->expects($this->any())->method('info')
                ->withConsecutive(['Successfully acquired the "{resource}" lock.'],
                    ['Expiration defined for "{resource}" lock for "{ttl}" seconds.'],
                    ['Retrieved a new mpx token {token} for user {username} that expires on {date}.', $this->callback(function ($context) {
                        try {
                            $this->assertArraySubset([
                                'token' => 'TOKEN-VALUE',
                                'username' => 'mpx/USER-NAME',
                            ], $context);
                            $this->assertMatchesRegularExpression('!\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{4}!', $context['date']);
                        } catch (ExpectationFailedException) {
                            return false;
                        }

                        return true;
                    })]
                );
        }

        return $logger;
    }
}
