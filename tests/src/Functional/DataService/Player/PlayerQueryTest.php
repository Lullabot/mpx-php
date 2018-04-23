<?php

namespace Lullabot\Mpx\Tests\Functional\DataService\Player;

use Lullabot\Mpx\DataService\ByFields;
use Lullabot\Mpx\DataService\DataObjectFactory;
use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\DataService\Range;
use Lullabot\Mpx\Tests\Functional\FunctionalTestBase;
use Psr\Http\Message\UriInterface;

/**
 * Tests loading Player objects.
 */
class PlayerQueryTest extends FunctionalTestBase
{
    /**
     * Test loading two player objects.
     */
    public function testQueryPlayer()
    {
        $manager = DataServiceManager::basicDiscovery();
        $dof = new DataObjectFactory($manager->getDataService('Player Data Service', 'Player', '1.6'), $this->authenticatedClient);
        $filter = new ByFields();
        $range = new Range();
        $range->setStartIndex(1)
            ->setEndIndex(2);
        $filter->setRange($range);
        $results = $dof->select($filter, $this->account);

        foreach ($results as $index => $result) {
            $this->assertInstanceOf(UriInterface::class, $result->getId());

            // Loading the object by itself.
            $reload = $dof->load($result->getId());
            $this->assertEquals($result, $reload->wait());
            if ($index > 2) {
                break;
            }
        }
    }
}
