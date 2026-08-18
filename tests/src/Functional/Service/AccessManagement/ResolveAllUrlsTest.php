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
        /** @var \Lullabot\Mpx\Service\AccessManagement\ResolveAllUrls $resolved */
        $resolver = new ResolveAllUrls($this->authenticatedClient);
        $resolved = $resolver->resolve('Media Data Service');
        $this->assertIsString($resolved->getService());
    }
}
