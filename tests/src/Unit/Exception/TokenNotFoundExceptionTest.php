<?php

namespace Lullabot\Mpx\Tests\Unit\Exception;

use Lullabot\Mpx\Exception\TokenNotFoundException;
use Lullabot\Mpx\User;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Lullabot\Mpx\Exception\TokenNotFoundException
 */
class TokenNotFoundExceptionTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        /** @var User|\PHPUnit_Framework_MockObject_MockObject $user */
        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();
        $user->method('getUsername')->willReturn('username');

        $e = new TokenNotFoundException($user);
        $this->assertEquals('Token not found for username.', $e->getMessage());
    }
}
