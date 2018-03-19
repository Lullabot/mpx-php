<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\Media;

use Lullabot\Mpx\DataService\Media\Media;
use Lullabot\Mpx\Tests\Unit\DataService\ObjectTestBase;

/**
 * Test the Media data object.
 *
 * @covers \Lullabot\Mpx\DataService\Media\Media
 */
class MediaTest extends ObjectTestBase
{
    protected $class = Media::class;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->loadFixture('media-object.json');
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
        $tests['added'] = ['added', \DateTime::createFromFormat('U.u', '1299622592.000')];
        $tests['updated'] = ['updated', \DateTime::createFromFormat('U.u', '1299624648.000')];
        $tests['availableDate'] = ['availableDate', \DateTime::createFromFormat('U.u', '1230796800.000')];
        $tests['expirationDate'] = ['expirationDate', \DateTime::createFromFormat('U.u', '1609401600.000')];
        $tests['pubDate'] = ['pubDate', \DateTime::createFromFormat('U.u', '1256661120.000')];

        return $tests;
    }
}
