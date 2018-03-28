<?php

namespace Lullabot\Mpx\Tests\Functional\Service\AccessManagement;

use Lullabot\Mpx\Service\AccessManagement\ResolveDomain;
use Lullabot\Mpx\Tests\Functional\FunctionalTestBase;
use Psr\Http\Message\UriInterface;

/**
 * Test calling resolveDomain.
 */
class ResolveDomainTest extends FunctionalTestBase
{
    /**
     * Tests that resolving domains returns an array.
     */
    public function testResolve()
    {
        $resolveDomain = new ResolveDomain($this->session);
        $resolved = $resolveDomain->resolve($this->account)->getResolveDomainResponse();
        $this->assertInternalType('array', $resolved);
        $this->assertNotEmpty($resolved);
        foreach ($resolved as $uri) {
            $this->assertInstanceOf(UriInterface::class, $uri);
        }
    }
}
