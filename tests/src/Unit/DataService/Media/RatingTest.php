<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\Media;

use Lullabot\Mpx\DataService\DataServiceExtractor;
use Lullabot\Mpx\DataService\Media\Rating;
use Lullabot\Mpx\Tests\Unit\DataService\ObjectTestBase;

/**
 * Test the Rating data object.
 *
 * @covers \Lullabot\Mpx\DataService\Media\Rating
 */
class RatingTest extends ObjectTestBase
{
    protected $class = Rating::class;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $dataServiceExtractor = new DataServiceExtractor();
        $dataServiceExtractor->setClass($this->class);
        $this->loadFixture('ratings-object.json', $dataServiceExtractor);
        $this->decoded = $this->decoded['ratings'][1];
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
