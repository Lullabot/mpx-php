<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\Media;

use Lullabot\Mpx\DataService\DataServiceExtractor;
use Lullabot\Mpx\DataService\Media\AvailabilityWindow;
use Lullabot\Mpx\DataService\Media\CategoryInfo;
use Lullabot\Mpx\DataService\Media\Media;
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

        $window1 = new AvailabilityWindow();
        $window1->setTargetAvailableDate(new \DateTime('2015-05-04T07:00:00.000000+0000'));
        $window1->setTargetExpirationDate(new \DateTime('2015-05-18T07:00:00.000000+0000'));
        $window1->setTargetAvailabilityTags(['desktop']);

        $window2 = new AvailabilityWindow();
        $window2->setTargetAvailableDate(new \DateTime('2015-05-04T07:00:00.000000+0000'));
        $window2->setTargetAvailabilityTags(['mobile']);
        $tests['availabilityWindows'] = ['availabilityWindows', [
            $window1,
            $window2,
        ]];

        $category1 = new CategoryInfo();
        $category1->setName('thePlatform/Ian Blaine');
        $category1->setScheme('thePlatform');
        $category2 = new CategoryInfo();
        $category2->setName('Technology');
        $category2->setScheme('urn:cat-scheme');
        $tests['categories'] = ['categories', [
            $category1,
            $category2,
        ]];

        return $tests;
    }
}
