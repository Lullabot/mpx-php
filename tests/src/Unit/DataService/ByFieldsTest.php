<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use Lullabot\Mpx\DataService\ByFields;
use Lullabot\Mpx\DataService\Range;
use Lullabot\Mpx\DataService\Sort;
use PHPUnit\Framework\TestCase;

/**
 * Test a ByFields query.
 *
 * @coversDefaultClass  \Lullabot\Mpx\DataService\ByFields
 */
class ByFieldsTest extends TestCase
{
    /**
     * Tests creating the ByFields query array.
     *
     * @covers ::__construct
     * @covers ::addField
     * @covers ::getFields
     * @covers ::setRange
     * @covers ::getRange
     * @covers ::setSort
     * @covers ::getSort
     * @covers ::toQueryParts
     */
    public function testToQueryParts()
    {
        $byFields = new ByFields();
        $byFields->addField('title', 'Most Excellent Video');

        $range = new Range();
        $range->setStartIndex(1)
            ->setEndIndex(10);
        $byFields->setRange($range);

        $byFields->sort()->addSort('id');

        $parts = $byFields->toQueryParts();
        $this->assertEquals([
            'byTitle' => 'Most Excellent Video',
            'sort' => 'id',
            'range' => '1-10',
        ], $parts);
    }

    /**
     * Test that a completely empty ByFields object can still be rendered to query parts.
     *
     * @covers ::toQueryParts
     */
    public function testNoValues()
    {
        $byFields = new ByFields();
        $this->assertEquals(['sort' => 'id', 'range' => '1-100'], $byFields->toQueryParts());
    }

    /**
     * Test the range return.
     *
     * @covers ::range
     */
    public function testRange()
    {
        $byFields = new ByFields();
        $range = $byFields->range();
        $this->assertSame($range, $byFields->getRange());
    }

    /**
     * Test the sort return.
     *
     * @covers ::sort
     */
    public function testSort()
    {
        $byFields = new ByFields();
        $sort = $byFields->sort();
        $this->assertSame($sort, $byFields->getSort());
    }
}
