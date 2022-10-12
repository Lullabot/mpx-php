<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\Media;

use Lullabot\Mpx\DataService\DataServiceExtractor;
use Lullabot\Mpx\DataService\DateTime\ConcreteDateTime;
use Lullabot\Mpx\DataService\Media\Release;
use Lullabot\Mpx\Tests\Unit\DataService\ObjectTestBase;

/**
 * Test the Release data object.
 *
 * @covers \Lullabot\Mpx\DataService\Media\Release
 */
class ReleaseTest extends ObjectTestBase
{
    protected $class = Release::class;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $dataServiceExtractor = new DataServiceExtractor();
        $dataServiceExtractor->setClass($this->class);
        $this->loadFixture('release-object.json', $dataServiceExtractor);
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

    public function getSetMethods()
    {
        $tests = parent::getSetMethods();

        $tests['added'] = ['added', new ConcreteDateTime(\DateTime::createFromFormat('U.u', '1299624648.000'))];
        $tests['updated'] = ['updated', new ConcreteDateTime(\DateTime::createFromFormat('U.u', '1299624648.000'))];

        return $tests;
    }
}
