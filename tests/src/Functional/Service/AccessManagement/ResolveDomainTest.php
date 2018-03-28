<?php

namespace Lullabot\Mpx\Tests\Functional\Service\AccessManagement;

use Lullabot\Mpx\Service\AccessManagement\ResolveDomain;
use Lullabot\Mpx\Tests\Functional\FunctionalTestBase;

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
        $resolved = $resolveDomain->resolve($this->account)->getResolved();
        $this->assertInternalType('array', $resolved);
        $this->assertNotEmpty($resolved);
    }
}
