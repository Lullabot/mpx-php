<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use Lullabot\Mpx\DataService\Access\Account;
use Lullabot\Mpx\DataService\DataObjectFactory;
use Lullabot\Mpx\DataService\ObjectList;
use Lullabot\Mpx\DataService\ObjectListQuery;
use PHPUnit\Framework\TestCase;

/**
 * Tests the list of objects.
 *
 * @covers \Lullabot\Mpx\DataService\ObjectList
 */
class ObjectListTest extends TestCase
{
    /**
     * @var ObjectList
     */
    protected $list;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->list = new ObjectList();
    }

    /**
     * Test all basic get / set methods.
     *
     * @param string $method The method base name to test.
     * @param mixed  $value  The value to set and get.
     * @dataProvider getSetData
     */
    public function testGetSet($method, $value)
    {
        $get = 'get'.ucfirst($method);
        $set = 'set'.ucfirst($method);
        $this->list->$set($value);
        $this->assertEquals($value, $this->list->$get());
    }

    /**
     * Data provider with basic get / set methods and data.
     */
    public function getSetData()
    {
        return [
            ['xmlNs', ['first', 'second']],
            ['startIndex', rand(1, getrandmax())],
            ['itemsPerPage', rand(1, getrandmax())],
            ['entryCount', rand(1, getrandmax())],
            ['entries', [new \stdClass()]],
            ['totalResults', rand(1, getrandmax())],
            ['objectListQuery', new ObjectListQuery()],
        ];
    }

    /**
     * Test calling getByFields when nothing is set.
     */
    public function testGetByFieldsMissing()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('This object list does not have an ObjectListQuery set.');
        $this->list->getObjectListQuery();
    }

    /**
     * Test if a list has a next list.
     */
    public function testHasNext()
    {
        $this->list->setEntries([new \stdClass()]);
        $this->list->setStartIndex(1);
        $this->list->setEntryCount(10);
        $this->list->setItemsPerPage(10);
        $this->list->setTotalResults(20);
        $this->assertTrue($this->list->hasNext());

        $this->list->setTotalResults(10);
        $this->assertFalse($this->list->hasNext());

        $this->list->setTotalResults(11);
        $this->list->setEntries([]);
        $this->assertFalse($this->list->hasNext());
    }

    /**
     * Test that a promise to a next list is returned.
     */
    public function testNextList()
    {
        $this->assertFalse($this->list->nextList());

        $this->list->setEntries([new \stdClass()]);
        $this->list->setStartIndex(30);
        $this->list->setEntryCount(10);
        $this->list->setItemsPerPage(10);
        $this->list->setTotalResults(50);

        /** @var DataObjectFactory $dof */
        $dof = $this->getMockBuilder(DataObjectFactory::class)->disableOriginalConstructor()->getMock();
        $account = new Account();
        $this->list->setDataObjectFactory($dof);
        $this->list->setObjectListQuery(new ObjectListQuery());

        $this->assertInstanceOf(PromiseInterface::class, $this->list->nextList());
    }

    /**
     * Test that a single item list has no next list.
     */
    public function testSingleItemList()
    {
        $this->list->setEntries([new \stdClass()]);
        $this->list->setStartIndex(1);
        $this->list->setEntryCount(1);
        $this->list->setItemsPerPage(1);

        /** @var DataObjectFactory $dof */
        $dof = $this->getMockBuilder(DataObjectFactory::class)->disableOriginalConstructor()->getMock();
        $account = new Account();
        $this->list->setDataObjectFactory($dof);
        $this->list->setObjectListQuery(new ObjectListQuery());
        $this->assertFalse($this->list->nextList());
    }

    /**
     * Test that an exception is thrown if a DataObjectFactory is not set.
     */
    public function testNextListNoDof()
    {
        $this->list->setEntries([new \stdClass()]);
        $this->list->setStartIndex(30);
        $this->list->setEntryCount(10);
        $this->list->setItemsPerPage(10);
        $this->list->setTotalResults(50);
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('setDataObjectFactory must be called before calling nextList.');
        $this->list->nextList();
    }

    /**
     * Test an exception is thrown if the previous fields are not set.
     */
    public function testNextListNoByFields()
    {
        $this->list->setEntries([new \stdClass()]);
        $this->list->setStartIndex(30);
        $this->list->setEntryCount(10);
        $this->list->setItemsPerPage(10);
        $this->list->setTotalResults(50);

        /** @var DataObjectFactory $dof */
        $dof = $this->getMockBuilder(DataObjectFactory::class)->disableOriginalConstructor()->getMock();
        $account = new Account();
        $this->list->setDataObjectFactory($dof);
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('setByFields must be called before calling nextList.');
        $this->list->nextList();
    }

    /**
     * Test array access exists.
     */
    public function testOffsetExists()
    {
        $this->list->setEntries([new \stdClass()]);
        $this->assertTrue($this->list->offsetExists(0));
        $this->assertFalse($this->list->offsetExists(1));
    }

    /**
     * Test fetching via array access.
     */
    public function testOffsetGet()
    {
        $class = new \stdClass();
        $this->list->setEntries([$class]);
        $this->assertSame($class, $this->list->offsetGet(0));
    }

    /**
     * Test setting via array access.
     */
    public function testOffsetSet()
    {
        $class = new \stdClass();
        $this->list->offsetSet(1, $class);
        $this->assertSame($class, $this->list->offsetGet(1));
    }

    /**
     * Test unsetting via array access.
     */
    public function testOffsetUnset()
    {
        $class = new \stdClass();
        $this->list->offsetSet(1, $class);
        $this->list->offsetUnset(1);
        $this->assertEmpty($this->list->getEntries());
    }

    /**
     * Test iterator implementations.
     */
    public function testIterator()
    {
        $first = new \stdClass();
        $second = new \stdClass();
        $this->list->setEntries([$first, $second]);

        $this->assertSame($first, $this->list->current());

        $this->list->next();
        $this->assertTrue($this->list->valid());
        $this->assertEquals($second, $this->list->current());
        $this->assertEquals(1, $this->list->key());

        $this->list->next();
        $this->assertFalse($this->list->valid());

        $this->list->rewind();
        $this->assertEquals(0, $this->list->key());
        $this->assertEquals($first, $this->list->current());
    }

    /**
     * Tests yielding lists.
     */
    public function testYieldLists()
    {
        $this->list->setEntries([new \stdClass()]);
        $this->list->setStartIndex(30);
        $this->list->setEntryCount(10);
        $this->list->setItemsPerPage(10);
        $this->list->setTotalResults(50);
        /** @var DataObjectFactory $dof */
        $dof = $this->getMockBuilder(DataObjectFactory::class)->disableOriginalConstructor()->getMock();
        $account = new Account();
        $this->list->setDataObjectFactory($dof);
        $this->list->setObjectListQuery(new ObjectListQuery());
        $y = $this->list->yieldLists();
        foreach ($y as $l) {
            $this->assertInstanceOf(PromiseInterface::class, $l);
            if (Promise::FULFILLED == $l->getState()) {
                $list = $l->wait();
                $this->assertEquals($this->list, $list);
            }
        }
    }

    /**
     * Tests when no data object factory is set.
     */
    public function testNoDataObjectFactory()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('setDataObjectFactory must be called before calling nextList.');
        $this->list->yieldLists()->current();
    }

    /**
     * Tests when no ObjectListQuery object is set.
     */
    public function testNoByFields()
    {
        /** @var DataObjectFactory $dof */
        $dof = $this->getMockBuilder(DataObjectFactory::class)->disableOriginalConstructor()->getMock();
        $account = new Account();
        $this->list->setDataObjectFactory($dof);
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('setByFields must be called before calling nextList.');
        $this->list->yieldLists()->current();
    }

    /**
     * Test setting the JSON representation.
     */
    public function testGetJson()
    {
        $json = ['key' => 'value'];
        $this->list->setJson(json_encode($json));
        $this->assertEquals($json, $this->list->getJson());
    }

    /**
     * Test an exception is thrown when no JSON is available.
     */
    public function testNoJson()
    {
        $this->expectException(\LogicException::class);
        $this->list->getJson();
    }
}
