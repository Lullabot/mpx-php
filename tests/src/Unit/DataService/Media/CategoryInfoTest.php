<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\Media;

use Lullabot\Mpx\DataService\DataServiceExtractor;
use Lullabot\Mpx\DataService\Media\CategoryInfo;
use Lullabot\Mpx\Tests\Unit\DataService\ObjectTestBase;

/**
 * Test the CategoryInfo object.
 *
 * @covers \Lullabot\Mpx\DataService\Media\CategoryInfo
 */
class CategoryInfoTest extends ObjectTestBase
{
    protected $class = CategoryInfo::class;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $dataServiceExtractor = new DataServiceExtractor();
        $dataServiceExtractor->setClass($this->class);
        $this->loadFixture('media-object.json', $dataServiceExtractor);
        $this->decoded = $this->decoded['categories'][0];
    }

    /**
     * Test get / set methods.
     *
     * @param string $field    The field on Player to test.
     * @param mixed  $expected (optional) Override the assertion if data is converted, such as with timestamps.
     *
     * @dataProvider getSetMethods
     */
    public function testGetSet(string $field, mixed $expected = null)
    {
        $this->assertObjectClass($this->class, $field, $expected);
    }
}
