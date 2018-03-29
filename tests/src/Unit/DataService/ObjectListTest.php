<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use GuzzleHttp\Promise\PromiseInterface;
use Lullabot\Mpx\DataService\Access\Account;
use Lullabot\Mpx\DataService\ByFields;
use Lullabot\Mpx\DataService\DataObjectFactory;
use Lullabot\Mpx\DataService\ObjectList;
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
    public function setUp()
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
            ['byFields', new ByFields()],
        ];
    }

    /**
     * Test calling getByFields when nothing is set.
     */
    public function testGetByFieldsMissing()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('This object list does not have byFields set.');
        $this->list->getByFields();
    }

    /**
     * Test if a list has a next list.
     */
    public function testHasNext()
    {
        $this->list->setEntries([new \stdClass()]);
        $this->list->setEntryCount(10);
        $this->list->setItemsPerPage(10);
        $this->assertTrue($this->list->hasNext());

        $this->list->setItemsPerPage(11);
        $this->assertFalse($this->list->hasNext());

        $this->list->setItemsPerPage(10);
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

        /** @var DataObjectFactory $dof */
        $dof = $this->getMockBuilder(DataObjectFactory::class)->disableOriginalConstructor()->getMock();
        $account = new Account();
        $this->list->setDataObjectFactory($dof, $account);
        $this->list->setByFields(new ByFields());

        $this->assertInstanceOf(PromiseInterface::class, $this->list->nextList());
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

        /** @var DataObjectFactory $dof */
        $dof = $this->getMockBuilder(DataObjectFactory::class)->disableOriginalConstructor()->getMock();
        $account = new Account();
        $this->list->setDataObjectFactory($dof, $account);
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
}
