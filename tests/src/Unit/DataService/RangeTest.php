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
        $start = random_int(1, mt_getrandmax());
        $entryCount = random_int(1, 10);
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
        $start = random_int(1, mt_getrandmax());
        $end = $start + random_int(1, 10);
        $parts = $range->setStartIndex($start)
            ->setEndIndex($end)
            ->toQueryParts();
        $this->assertEquals(['range' => $start.'-'.$end], $parts);
    }

    /**
     * @covers ::toQueryParts
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
     * @covers ::nextRanges
     */
    public function testNextRanges()
    {
        $list = new ObjectList();
        $start = random_int(1, mt_getrandmax());
        $entryCount = random_int(1, 10);
        $list->setStartIndex($start);
        $list->setEntryCount($entryCount);
        $list->setItemsPerPage($entryCount);
        $remainingPages = random_int(1, 10);
        $list->setTotalResults($start + ($entryCount * $remainingPages));
        $ranges = Range::nextRanges($list);

        $this->assertEquals($remainingPages, \count($ranges));

        foreach ($ranges as $range) {
            $start += $entryCount;
            $end = min($start + $entryCount - 1, $list->getTotalResults());
            $this->assertEquals("$start-$end", $range->toQueryParts()['range']);
        }
    }

    /**
     * Tests that we return the right number of ranges when the last range is not a complete page.
     *
     * @covers ::nextRanges
     */
    public function testPartialEndRange()
    {
        $list = new ObjectList();
        $list->setStartIndex(1);
        $list->setItemsPerpage(2);
        $list->setEntryCount(2);
        $list->setTotalResults(79425);
        $ranges = Range::nextRanges($list);
        $this->assertCount(39712, $ranges);
        $this->assertEquals('3-4', reset($ranges)->toQueryParts()['range']);
        $this->assertEquals('79425-79425', end($ranges)->toQueryParts()['range']);
    }

    /**
     * Test that no ranges are returned if we are already on the last page.
     *
     * @covers ::nextRanges
     */
    public function testNextOnLastRange()
    {
        $list = new ObjectList();
        $list->setStartIndex(11);
        $list->setItemsPerPage(10);
        $list->setEntryCount(10);
        $list->setTotalResults(20);
        $ranges = Range::nextRanges($list);
        $this->assertCount(0, $ranges);
    }
}
