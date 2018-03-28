<?php

namespace Lullabot\Mpx\Tests\Unit\Exception;

use Lullabot\Mpx\Exception\TokenNotFoundException;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
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
        /** @var \Lullabot\Mpx\Service\IdentityManagement\UserSession|\PHPUnit_Framework_MockObject_MockObject $user */
        $user = $this->getMockBuilder(UserSession::class)
            ->disableOriginalConstructor()
            ->getMock();
        $user->method('getUsername')->willReturn('username');

        $e = new TokenNotFoundException($user);
        $this->assertEquals('Token not found for username.', $e->getMessage());
    }
}
