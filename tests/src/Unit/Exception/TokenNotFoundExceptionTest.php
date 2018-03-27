<?php

namespace Lullabot\Mpx\Tests\Unit\Exception;

use Lullabot\Mpx\Exception\TokenNotFoundException;
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
        $user = new \Lullabot\Mpx\User('username', 'password');
        $e = new TokenNotFoundException($user);
        $this->assertEquals('Token not found for username.', $e->getMessage());
    }
}
