<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\Player;

use Lullabot\Mpx\DataService\DataServiceExtractor;
use Lullabot\Mpx\DataService\Player\PlugInInstance;
use Lullabot\Mpx\Tests\Unit\DataService\ObjectTestBase;

/**
 * Test the PlugInInstance data object.
 *
 * @covers \Lullabot\Mpx\DataService\Player\PlugInInstance
 */
class PlugInInstanceTest extends ObjectTestBase
{
    protected $class = PlugInInstance::class;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $dataServiceExtractor = new DataServiceExtractor();
        $dataServiceExtractor->setClass($this->class);
        $this->loadFixture('player-object.json', $dataServiceExtractor);
        $this->decoded = $this->decoded['enabledPlugIns'][0];
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

    /**
     * @param string $class
     *
     * @return array
     */
    public function getSetMethods()
    {
        return parent::getSetMethods();
    }
}
