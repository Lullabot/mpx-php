<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use Lullabot\Mpx\DataService\ByFields;
use Lullabot\Mpx\DataService\ObjectListQuery;
use Lullabot\Mpx\DataService\Range;
use Lullabot\Mpx\DataService\Sort;
use PHPUnit\Framework\TestCase;

/**
 * Test a ObjectListQuery query.
 *
 * @coversDefaultClass  \Lullabot\Mpx\DataService\ObjectListQuery
 */
class ObjectListQueryTest extends TestCase
{
    /**
     * Tests creating the ObjectListQuery query array.
     *
     * @covers ::__construct
     * @covers ::getFields
     * @covers ::setRange
     * @covers ::getRange
     * @covers ::setSort
     * @covers ::getSort
     * @covers ::toQueryParts
     */
    public function testToQueryParts()
    {
        $query = new ObjectListQuery();
        $byFields = new ByFields();
        $byFields->addField('title', 'Most Excellent Video');
        $query->add($byFields);

        $range = new Range();
        $range->setStartIndex(1)
            ->setEndIndex(10);
        $query->setRange($range);

        $sort = new Sort();
        $sort->addSort('id');
        $query->setSort($sort);

        $parts = $query->toQueryParts();
        $this->assertEquals([
            'byTitle' => 'Most Excellent Video',
            'sort' => 'id',
            'range' => '1-10',
        ], $parts);
    }

    /**
     * Test that a completely empty ObjectListQuery object can still be rendered to query parts.
     *
     * @covers ::toQueryParts
     */
    public function testNoValues()
    {
        $byFields = new ObjectListQuery();
        $this->assertEquals(['sort' => 'id', 'range' => '1-100'], $byFields->toQueryParts());
    }
}
