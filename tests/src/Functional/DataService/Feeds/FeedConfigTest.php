<?php

namespace Lullabot\Mpx\Tests\Functional\DataService\Feeds;

use Lullabot\Mpx\DataService\DataObjectFactory;
use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\DataService\ObjectListQuery;
use Lullabot\Mpx\Tests\Functional\FunctionalTestBase;
use Psr\Http\Message\UriInterface;

/**
 * Tests loading feed configuration objects.
 */
class FeedConfigTest extends FunctionalTestBase
{
    /**
     * Tests loading two FeedConfig objects.
     */
    public function testQueryFeeds()
    {
        $manager = DataServiceManager::basicDiscovery();
        $service = $manager->getDataService('Feeds Data Service', 'FeedConfig', '2.2');
        $dof = new DataObjectFactory($service, $this->authenticatedClient);
        $filter = new ObjectListQuery();
        $filter->getRange()->setStartIndex(1)
            ->setEndIndex(2);
        $results = $dof->select($filter);

        foreach ($results as $index => $result) {
            $this->assertInstanceOf(UriInterface::class, $result->getId());
            if ($index + 1 > 2) {
                break;
            }
        }
    }
}
