<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\Feeds;

use Lullabot\Mpx\DataService\DataServiceExtractor;
use Lullabot\Mpx\DataService\Feeds\SortKey;
use Lullabot\Mpx\DataService\Feeds\SubFeed;
use Lullabot\Mpx\DataService\Media\Rating;
use Lullabot\Mpx\Tests\Unit\DataService\ObjectTestBase;

/**
 * Test the SubFeed data object.
 *
 * @covers \Lullabot\Mpx\DataService\Feeds\SubFeed
 */
class SubFeedTest extends ObjectTestBase
{
    protected $class = SubFeed::class;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $dataServiceExtractor = new DataServiceExtractor();
        $dataServiceExtractor->setClass($this->class);
        $this->loadFixture('subfeed-object.json', $dataServiceExtractor);
    }

    /**
     * Test get / set methods.
     *
     * @param string $field    The field on Player to test.
     * @param mixed  $expected (optional) Override the assertion if data is converted, such as with timestamps.
     *
     * @dataProvider getSetMethods
     */
    public function testGetSet(string $field, $expected = null)
    {
        $this->assertObjectClass($this->class, $field, $expected);
    }
}
