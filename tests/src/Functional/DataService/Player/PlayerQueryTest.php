<?php

namespace Lullabot\Mpx\Tests\Functional\DataService\Player;

use Lullabot\Mpx\DataService\ObjectListQuery;
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
        $filter = new ObjectListQuery();
        $range = new Range();
        $range->setStartIndex(1)
            ->setEndIndex(2);
        $filter->setRange($range);
        $results = $dof->select($filter, $this->account);

        foreach ($results as $index => $result) {
            $this->assertInstanceOf(UriInterface::class, $result->getId());

            // Loading the object by itself.
            $reload = $dof->load($result->getId());
            $item = $reload->wait();

            // We need to override the JSON strings. While the strings may be
            // functionally identical, the namespace prefixes in the responses
            // can change between requests.
            $result->setJson('{}');
            $item->setJson('{}');
            $this->assertEquals($result, $item);
            if ($index + 1 > 2) {
                break;
            }
        }
    }
}
