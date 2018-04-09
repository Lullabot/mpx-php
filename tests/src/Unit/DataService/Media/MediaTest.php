<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\Media;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\DataServiceExtractor;
use Lullabot\Mpx\DataService\Media\AvailabilityWindow;
use Lullabot\Mpx\DataService\Media\CategoryInfo;
use Lullabot\Mpx\DataService\Media\Chapter;
use Lullabot\Mpx\DataService\Media\Credit;
use Lullabot\Mpx\DataService\Media\Media;
use Lullabot\Mpx\DataService\Media\MediaFile;
use Lullabot\Mpx\DataService\Media\PreviousLocation;
use Lullabot\Mpx\Tests\Unit\DataService\ObjectTestBase;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;

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
        $dataServiceExtractor = new DataServiceExtractor();
        $dataServiceExtractor->setClass($this->class);
        $this->loadFixture('media-object.json', $dataServiceExtractor);
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
     * Test methods with subobjects, where we leave testing of those objects to their own tests.
     *
     * @param string $field
     * @param string $class
     *
     * @dataProvider instanceOfDataProvider
     */
    public function testInstanceOf($field, $class) {
        $object = $this->deserialize($this->class, $field);
        $method = 'get'.ucfirst($field);
        foreach($object->$method() as $chapter) {
            $this->assertInstanceOf($class, $chapter);
        }
    }

    /**
     * Return methods that we only test the instance of each subobject.
     */
    public function instanceOfDataProvider() {
        return [
            ['availabilityWindows', AvailabilityWindow::class],
            ['chapters', Chapter::class],
            ['categories', CategoryInfo::class],
            ['content', MediaFile::class],
            ['thumbnails', MediaFile::class],
            ['credits', Credit::class],
        ];
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

        unset($tests['availabilityWindows']);
        unset($tests['categories']);
        unset($tests['chapters']);
        unset($tests['content']);
        unset($tests['credits']);
        unset($tests['thumbnails']);
        return $tests;
    }
}
