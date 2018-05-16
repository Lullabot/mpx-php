<?php

namespace Lullabot\Mpx\Tests\Functional\Service\AccessManagement;

use Lullabot\Mpx\Service\AccessManagement\ResolveDomain;
use Lullabot\Mpx\Tests\Functional\FunctionalTestBase;
use Psr\Http\Message\UriInterface;

/**
 * Test calling resolveDomain.
 *
 * @coversDefaultClass \Lullabot\Mpx\Service\AccessManagement\ResolveDomain
 */
class ResolveDomainTest extends FunctionalTestBase
{
    /**
     * Tests that resolving domains returns an array.
     *
     * @covers \Lullabot\Mpx\Service\AccessManagement\ResolveBase
     */
    public function testResolve()
    {
        $resolveDomain = new ResolveDomain($this->authenticatedClient);
        $resolved = $resolveDomain->resolve($this->account);
        $this->assertNotEmpty($resolved->getServices());

        foreach ($resolved->getServices() as $service) {
            $uri = $resolved->getUrl($service);
            $this->assertInstanceOf(UriInterface::class, $uri);
            $this->assertEquals('https', $uri->getScheme());
        }
    }
}
