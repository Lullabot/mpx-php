<?php

namespace Lullabot\Mpx\Tests\Functional\DataService\Media;

use Lullabot\Mpx\DataService\ObjectListQuery;
use Lullabot\Mpx\DataService\DataObjectFactory;
use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\Tests\Functional\FunctionalTestBase;
use Psr\Http\Message\UriInterface;

/**
 * Tests loading of media files.
 */
class MediaFileTest extends FunctionalTestBase
{
    /**
     * Tests querying for the first two media files.
     */
    public function testQueryMediaFile()
    {
        $manager = DataServiceManager::basicDiscovery();
        $service = $manager->getDataService('Media Data Service', 'MediaFile', '1.10');
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
