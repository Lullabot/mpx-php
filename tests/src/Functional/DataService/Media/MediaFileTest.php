<?php

namespace Lullabot\Mpx\Tests\Functional\DataService\Media;

use Lullabot\Mpx\DataService\ByFields;
use Lullabot\Mpx\DataService\DataObjectFactory;
use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\DataService\Range;
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
        $filter = new ByFields();
        $range = new Range();
        $range->setStartIndex(1)
            ->setEndIndex(2);
        $filter->setRange($range);
        $results = $dof->select($filter, $this->account);

        foreach ($results as $index => $result) {
            $this->assertInstanceOf(UriInterface::class, $result->getId());
            if ($index > 2) {
                break;
            }
        }
    }
}
