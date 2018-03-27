<?php

namespace Lullabot\Mpx\Tests\Unit;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Lullabot\Mpx\Exception\ClientException;
use Lullabot\Mpx\Tests\JsonResponse;
use Lullabot\Mpx\Tests\MockClientTrait;
use Lullabot\Mpx\TokenCachePool;
use Lullabot\Mpx\User;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Lock\Exception\LockConflictedException;
use Symfony\Component\Lock\StoreInterface;

/**
 * Tests MPX user accounts.
 *
 * @coversDefaultClass \Lullabot\Mpx\User
 */
class UserTest extends TestCase
{
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
        /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        $logger = $this->fetchTokenLogger(1);

        $user = new User($client, $store, $tokenCachePool, $logger, 'USER-NAME', 'correct-password');
        $token = $user->acquireToken();
        $this->assertEquals($token, $tokenCachePool->getToken($user));
        $user->signOut();
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

        /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        /** @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();

        $logger->expects($this->at(0))->method('info')
            ->with('Successfully acquired the "{resource}" lock.');

        $user = new User($client, $store, new TokenCachePool(new ArrayCachePool()), $logger, 'USER-NAME', 'incorrect-password');
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage("Error com.theplatform.authentication.api.exception.AuthenticationException: Either 'USER-NAME' does not have an account with this site, or the password was incorrect.");
        $this->expectExceptionCode(401);
        $user->acquireToken();
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
        /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $logger */
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

        $user = new User($client, $store, $tokenCachePool, $logger, 'USER-NAME', 'correct-password');
        $first_token = $user->acquireToken();
        $this->assertEquals($first_token, $tokenCachePool->getToken($user));
        $second_token = $user->acquireToken(null, true);
        $this->assertEquals($second_token, $tokenCachePool->getToken($user));
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
        /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        $store->expects($this->once())->method('waitAndSave')
            ->willThrowException(new LockConflictedException());
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());

        // We cover logging in other tests.
        $logger = new NullLogger();

        $user = new User($client, $store, $tokenCachePool, $logger, 'USER-NAME', 'correct-password');
        $this->expectException(LockConflictedException::class);
        $user->acquireToken();
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
