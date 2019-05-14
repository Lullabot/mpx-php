<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use GuzzleHttp\Promise\Promise;
use Lullabot\Mpx\DataService\ObjectList;
use Lullabot\Mpx\DataService\ObjectListIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass  \Lullabot\Mpx\DataService\ObjectListIterator
 */
class ObjectListIteratorTest extends TestCase
{
    /**
     * Test basic functioning of the iterator with a single page of results.
     *
     * @covers ::__construct
     * @covers ::key
     * @covers ::current
     * @covers ::next
     * @covers ::valid
     */
    public function testCurrent()
    {
        // Create a single page of results with two entries.
        $list = new ObjectList();
        $first = new \stdClass();
        $second = new \stdClass();

        $list->setEntries([$first, $second]);
        $list->setItemsPerPage(2);
        $list->setTotalResults(2);

        $promise = new Promise();
        $promise->resolve($list);
        $iterator = new ObjectListIterator($promise);

        // Check fetching the first item in the list.
        $this->assertTrue($iterator->valid());
        $this->assertEquals(0, $iterator->key());
        $this->assertSame($first, $iterator->current());

        // Check fetching the second item in the list.
        $iterator->next();
        $iterator->valid();
        $this->assertSame($second, $iterator->current());
        $this->assertEquals(1, $iterator->key());
        $this->assertEquals(2, $iterator->getTotalResults());
    }

    /**
     * Test iterating over multiple pages of results.
     *
     * @covers ::valid
     */
    public function testNext()
    {
        /** @var ObjectList|\PHPUnit_Framework_MockObject_MockObject $list */
        $list = $this->getMockBuilder(ObjectList::class)
            ->getMock();
        $first_promise = new Promise();
        $first_promise->resolve($list);

        // Each page has a single result.
        $list->method('getItemsPerPage')->willReturn(1);

        /** @var ObjectList|\PHPUnit_Framework_MockObject_MockObject $second_list */
        $second_list = $this->getMockBuilder(ObjectList::class)
            ->getMock();
        $second_promise = new Promise();
        $second_promise->resolve($second_list);

        // This is a complete list - full, and no further lists exist.
        $second_list->method('getItemsPerPage')->willReturn(1);
        $second_list->method('offsetExists')->with(0)->willReturn(true);

        $list->method('nextList')->willReturn($second_promise);

        $iterator = new ObjectListIterator($first_promise);
        $iterator->next();
        $this->assertTrue($iterator->valid());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * Tests returning "end of list" when the last page does not have the
     * maximum number of results.
     *
     * @covers ::valid
     */
    public function testNextPartialPage()
    {
        /** @var ObjectList|\PHPUnit_Framework_MockObject_MockObject $list */
        $list = $this->getMockBuilder(ObjectList::class)
            ->getMock();
        $first_promise = new Promise();
        $first_promise->resolve($list);
        $list->method('getItemsPerPage')->willReturn(2);

        /** @var ObjectList|\PHPUnit_Framework_MockObject_MockObject $second_list */
        $second_list = $this->getMockBuilder(ObjectList::class)
            ->getMock();
        $second_promise = new Promise();
        $second_promise->resolve($second_list);
        $second_list->method('getItemsPerPage')->willReturn(2);
        $second_list->method('offsetExists')->with(1)->willReturn(false);
        $list->method('nextList')->willReturn($second_promise);

        // The total result set is three items, split into two pages of two.
        $iterator = new ObjectListIterator($first_promise);

        // Iterate through the first next two results, going to the second page.
        $iterator->next();
        $iterator->next();

        // Iterate beyond the end of the list, to the empty "fourth" position.
        $iterator->next();
        $this->assertFalse($iterator->valid());
    }
}
