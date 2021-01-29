<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\Media;

use Lullabot\Mpx\DataService\DataServiceExtractor;
use Lullabot\Mpx\DataService\DateTime\ConcreteDateTime;
use Lullabot\Mpx\DataService\Media\MediaFile;
use Lullabot\Mpx\DataService\Media\PreviousLocation;
use Lullabot\Mpx\DataService\Media\Release;
use Lullabot\Mpx\DataService\Media\TransferInfo;
use Lullabot\Mpx\Tests\Unit\DataService\ObjectTestBase;

/**
 * Test the Media data object.
 *
 * As PreviousLocation and TransferInfo are dependent objects, we test them within MediaFile.
 *
 * @covers \Lullabot\Mpx\DataService\Media\MediaFile
 * @covers \Lullabot\Mpx\DataService\Media\PreviousLocation
 * @covers \Lullabot\Mpx\DataService\Media\TransferInfo
 */
class MediaFileTest extends ObjectTestBase
{
    protected $class = MediaFile::class;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->loadFixture('mediafile-object.json', new DataServiceExtractor());
    }

    /**
     * Test get / set methods.
     *
     * @param string $field    The field on MediaFile to test.
     * @param mixed  $expected (optional) Override the assertion if data is converted, such as with timestamps.
     *
     * @dataProvider getSetMethods
     */
    public function testGetSet(string $field, $expected = null)
    {
        $this->assertObjectClass($this->class, $field, $expected);
    }

    /**
     * Test methods with subobjects, where we leave testing of those objects to their own tests.
     *
     * @param string $field
     * @param string $class
     *
     * @dataProvider instanceOfDataProvider
     */
    public function testInstanceOf($field, $class)
    {
        $object = $this->deserialize($this->class, $field);
        $method = 'get'.ucfirst($field);
        $value = $object->$method();
        if (\is_array($value)) {
            foreach ($value as $item) {
                $this->assertInstanceOf($class, $item);
            }
        } else {
            $this->assertInstanceOf($class, $value);
        }
    }

    /**
     * Return methods that we only test the instance of each subobject.
     */
    public function instanceOfDataProvider()
    {
        return [
            ['transferInfo', TransferInfo::class],
            ['previousLocations', PreviousLocation::class],
            ['releases', Release::class],
        ];
    }

    /**
     * @return array
     */
    public function getSetMethods()
    {
        $tests = parent::getSetMethods();
        $tests['added'] = ['added', new ConcreteDateTime(\DateTime::createFromFormat('U.u', '1299623178.000'))];
        $tests['updated'] = ['updated', new ConcreteDateTime(\DateTime::createFromFormat('U.u', '1299624648.000'))];

        unset($tests['transferInfo']);
        unset($tests['previousLocations']);
        unset($tests['releases']);

        return $tests;
    }
}
