<?php

namespace Lullabot\Mpx\Tests\Functional\DataService;

use Lullabot\Mpx\DataService\DataObjectFactory;
use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\DataService\Fields;
use Lullabot\Mpx\DataService\ObjectListQuery;
use Lullabot\Mpx\DataService\Range;
use Lullabot\Mpx\Tests\Functional\FunctionalTestBase;

class FieldsTest extends FunctionalTestBase
{
    public function testLimitFields()
    {
        $manager = DataServiceManager::basicDiscovery();
        $mediaDataService = $manager->getDataService('Media Data Service', 'Media', '1.10');
        $dof = new DataObjectFactory($mediaDataService->getAnnotation()->getFieldDataService(), $this->authenticatedClient);
        $filter = new ObjectListQuery();
        $fields = (new Fields())->addField('title');
        $filter->add($fields);
        $filter->setRange((new Range())->setStartIndex(1)->setEndIndex(1));
        $results = $dof->select($filter);
        $results->valid();
        $result = $results->current();
        $json = $result->getJson();
        $this->assertCount(1, $json);
        $this->assertArrayHasKey('title', $json);
    }
}
