<?php

namespace Lullabot\Mpx\Tests\Unit\Exception;

use Lullabot\Mpx\Exception\TokenNotFoundException;
use Lullabot\Mpx\Service\IdentityManagement\User;
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
        /** @var UserSession|\PHPUnit_Framework_MockObject_MockObject $userSession */
        $userSession = $this->getMockBuilder(UserSession::class)
            ->disableOriginalConstructor()
            ->getMock();
        $userSession->method('getUser')->willReturn(new User('mpx/username', 'correct-password'));

        $e = new TokenNotFoundException($userSession);
        $this->assertEquals('Token not found for mpx/username.', $e->getMessage());
    }
}
