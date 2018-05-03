<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use Lullabot\Mpx\DataService\ObjectList;
use Lullabot\Mpx\DataService\Range;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Lullabot\Mpx\DataService\Range
 */
class RangeTest extends TestCase
{
    /**
     * Test fetching the next range based on an existing range.
     *
     * @covers ::nextRange
     */
    public function testNextRange()
    {
        $list = new ObjectList();
        $start = rand(1, getrandmax());
        $entryCount = rand(1, 10);
        $list->setStartIndex($start);
        $list->setEntryCount($entryCount);
        $list->setItemsPerPage($entryCount);
        $range = Range::nextRange($list);

        $this->assertEquals(['range' => ($start + $entryCount).'-'.($start + $entryCount - 1 + $entryCount)], $range->toQueryParts());
    }

    /**
     * Test converting a range to query parts.
     *
     * @covers ::setStartIndex
     * @covers ::setEndIndex
     * @covers ::toQueryParts
     */
    public function testToQueryParts()
    {
        $range = new Range();
        $start = rand(1, getrandmax());
        $end = $start + rand(1, 10);
        $parts = $range->setStartIndex($start)
            ->setEndIndex($end)
            ->toQueryParts();
        $this->assertEquals(['range' => $start.'-'.$end], $parts);
    }

    /**
     * @covers ::toQueryParts()
     */
    public function testEmpty()
    {
        $range = new Range();
        $this->assertEquals([], $range->toQueryParts());
    }

    /**
     * @covers ::setStartIndex
     */
    public function testBadStartIndex()
    {
        $range = new Range();
        $this->expectException(\RangeException::class);
        $this->expectExceptionMessage('The start index must be 1 or greater.');
        $range->setStartIndex(0);
    }

    /**
     * @covers ::setEndIndex
     */
    public function testBadEndIndex()
    {
        $range = new Range();
        $this->expectException(\RangeException::class);
        $this->expectExceptionMessage('The end index must be 1 or greater.');
        $range->setEndIndex(0);
    }

    /**
     * Tests generating all ranges in the result set.
     *
     * @covers ::nextRanges()
     */
    public function testNextRanges()
    {
        $list = new ObjectList();
        $start = rand(1, getrandmax());
        $entryCount = rand(1, 10);
        $list->setStartIndex($start);
        $list->setEntryCount($entryCount);
        $list->setItemsPerPage($entryCount);
        $pages = rand(1, 10);
        $list->setTotalResults($entryCount * $pages);
        $ranges = Range::nextRanges($list);

        $this->assertEquals($pages, count($ranges));

        foreach ($ranges as $range) {
            $start += $entryCount;
            $end = $start + $entryCount - 1;
            $this->assertEquals("$start-$end", $range->toQueryParts()['range']);
        }
    }
}
