<?php

namespace Lullabot\Mpx\Tests\Functional\DataService;

use Lullabot\Mpx\DataService\ObjectListQuery;
use Lullabot\Mpx\DataService\DataObjectFactory;
use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\DataService\Field;
use Lullabot\Mpx\DataService\Range;
use Lullabot\Mpx\Tests\Functional\FunctionalTestBase;

/**
 * Tests loading custom fields.
 */
class CustomFieldTest extends FunctionalTestBase
{
    /**
     * Tests loading custom field definitions.
     */
    public function testCustomFields()
    {
        $manager = DataServiceManager::basicDiscovery();
        $mediaDataService = $manager->getDataService('Media Data Service', 'Media', '1.10');
        $dof = new DataObjectFactory($mediaDataService->getAnnotation()->getFieldDataService(), $this->authenticatedClient);
        $filter = new ObjectListQuery();
        $range = new Range();
        $range->setStartIndex(1)
            ->setEndIndex(2);
        $filter->setRange($range);
        $results = $dof->select($filter, $this->account);
        foreach ($results as $index => $field) {
            $this->assertInstanceOf(Field::class, $field);

            // Loading the object by itself.
            $reload = $dof->load($field->getId());
            $this->assertEquals($field, $reload->wait());
            if ($index + 1 > 2) {
                break;
            }
        }
    }
}
