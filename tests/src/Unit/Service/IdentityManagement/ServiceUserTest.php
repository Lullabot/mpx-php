<?php

namespace Lullabot\Mpx\Tests\Unit\Service\IdentityManagement;

use Lullabot\Mpx\Service\IdentityManagement\ServiceCredentialsInterface;
use Lullabot\Mpx\Service\IdentityManagement\ServiceUser;
use Lullabot\Mpx\Service\IdentityManagement\UserInterface;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Lullabot\Mpx\Service\IdentityManagement\ServiceUser
 */
class ServiceUserTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getMpxClientId
     * @covers ::getMpxClientSecret
     */
    public function testCredentials(): void
    {
        $user = new ServiceUser('CLIENT-ID', 'CLIENT-SECRET');

        $this->assertEquals('CLIENT-ID', $user->getMpxClientId());
        $this->assertEquals('CLIENT-SECRET', $user->getMpxClientSecret());
        $this->assertInstanceOf(ServiceCredentialsInterface::class, $user);
        $this->assertInstanceOf(UserInterface::class, $user);
    }

    /**
     * Test that the credentials are also exposed as a username and password.
     *
     * @covers ::getMpxUsername
     * @covers ::getMpxPassword
     */
    public function testUserInterfaceAliases(): void
    {
        $user = new ServiceUser('CLIENT-ID', 'CLIENT-SECRET');

        $this->assertEquals('CLIENT-ID', $user->getMpxUsername());
        $this->assertEquals('CLIENT-SECRET', $user->getMpxPassword());
    }

    /**
     * Test that an OAuth client ID needs no leading directory, unlike User.
     *
     * @covers ::__construct
     */
    public function testClientIdNeedsNoDirectory(): void
    {
        $user = new ServiceUser('0oazmvvnwr8dxUG9J417', 'CLIENT-SECRET');
        $this->assertEquals('0oazmvvnwr8dxUG9J417', $user->getMpxClientId());
    }

    /**
     * @covers ::__construct
     */
    public function testEmptyClientId(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The mpx service client ID must not be empty.');
        new ServiceUser('', 'CLIENT-SECRET');
    }
}
