<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use Lullabot\Mpx\DataService\ByFields;
use PHPUnit\Framework\TestCase;

/**
 * Tests creating a ByFields query.
 *
 * @covers \Lullabot\Mpx\DataService\ByFields
 */
class ByFieldsTest extends TestCase
{
    public function testToQueryParts()
    {
        $fields = new ByFields();
        $fields->addField('title', 'value');
        $fields->addField('series', 'value2');
        $this->assertEquals([
            'byTitle' => 'value',
            'bySeries' => 'value2',
        ], $fields->toQueryParts());
    }
}
