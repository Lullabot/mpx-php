<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use Lullabot\Mpx\DataService\Fields;
use PHPUnit\Framework\TestCase;

/**
 * Tests creating a Field query.
 *
 * @covers \Lullabot\Mpx\DataService\Fields
 */
class FieldsTest extends TestCase
{
    public function testToQueryParts()
    {
        $fields = new Fields();
        $fields->addField('id')
            ->addField('title')
            ->addField('description');

        $this->assertEquals([
            'fields' => 'id,title,description',
        ], $fields->toQueryParts());
    }
}
