<?php

namespace Lullabot\Mpx\Tests\Functional\DataService\Media;

use Lullabot\Mpx\DataService\ByFields;
use Lullabot\Mpx\DataService\DataObjectFactory;
use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\DataService\ObjectInterface;
use Lullabot\Mpx\DataService\ObjectList;
use Lullabot\Mpx\DataService\Range;
use Lullabot\Mpx\Tests\Functional\FunctionalTestBase;
use Psr\Http\Message\UriInterface;

/**
 * Tests loading Media objects.
 */
class MediaQueryTest extends FunctionalTestBase
{
    /**
     * Test loading two media objects.
     */
    public function testQueryMedia()
    {
        $manager = DataServiceManager::basicDiscovery();
        $dof = new DataObjectFactory($manager->getDataService('Media Data Service', 'Media', '1.10'), $this->authenticatedClient);
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
            /** @var ObjectInterface $item */
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

    /**
     * Loads the most recently uploaded media items.
     *
     * The fastest way to do this is to add a sort on the initial query.
     * However, a bug was found in our range calculation when test code worked
     * this way, which while not optimal should still work.
     */
    public function testLoadRecentContent()
    {
        $manager = DataServiceManager::basicDiscovery();
        $dof = new DataObjectFactory($manager->getDataService('Media Data Service', 'Media', '1.10'), $this->authenticatedClient);
        $filter = new ByFields();
        $range = new Range();
        $range->setStartIndex(1)
            ->setEndIndex(2);
        $filter->setRange($range);
        /** @var ObjectList $list */
        $list = $dof->selectRequest($filter, $this->account)->wait();

        // Determine the end of the list of ranges.
        $ranges = Range::nextRanges($list);
        $filter->setRange(end($ranges));
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
        }
    }
}
