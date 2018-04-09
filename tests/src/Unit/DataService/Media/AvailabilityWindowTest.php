<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\Media;

use Lullabot\Mpx\DataService\DataServiceExtractor;
use Lullabot\Mpx\DataService\Media\AvailabilityWindow;
use Lullabot\Mpx\DataService\Media\Media;
use Lullabot\Mpx\Tests\Unit\DataService\ObjectTestBase;

/**
 * Test the Media data object.
 *
 * @covers \Lullabot\Mpx\DataService\Media\AvailabilityWindow
 */
class AvailabilityWindowTest extends ObjectTestBase
{
    protected $class = AvailabilityWindow::class;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $dataServiceExtractor = new DataServiceExtractor();
        $dataServiceExtractor->setClass($this->class);
        $this->loadFixture('media-object.json', $dataServiceExtractor);
        $this->decoded = $this->decoded['availabilityWindows'][0];
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
        $tests = parent::getSetMethods();
        $tests['targetAvailableDate'] = ['targetAvailableDate', \DateTime::createFromFormat('U.u', '1430722800.000')];
        $tests['targetExpirationDate'] = ['targetExpirationDate', \DateTime::createFromFormat('U.u', '1431932400.000')];

        return $tests;
    }
}
