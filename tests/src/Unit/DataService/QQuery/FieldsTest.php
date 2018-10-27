<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\QQuery;

use Lullabot\Mpx\DataService\QQuery\Fields;
use PHPUnit\Framework\TestCase;

/**
 * Tests creating a Field query.
 *
 * @covers \Lullabot\Mpx\DataService\QQuery\Fields
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
