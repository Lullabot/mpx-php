<?php

namespace Lullabot\Mpx\Tests\Functional\Service\AccessManagement;

use Lullabot\Mpx\Service\AccessManagement\ResolveAllUrls;
use Lullabot\Mpx\Tests\Functional\FunctionalTestBase;

/**
 * Test resolving URLs with a live API call.
 */
class ResolveAllUrlsTest extends FunctionalTestBase
{
    /**
     * Execute a resolveAllUrls() call.
     */
    public function testResolve()
    {
        /** @var ResolveAllUrls $resolved */
        $resolved = ResolveAllUrls::load($this->authenticatedClient, 'Media Data Service')->wait();
        $this->assertInternalType('string', $resolved->getService());
    }
}
