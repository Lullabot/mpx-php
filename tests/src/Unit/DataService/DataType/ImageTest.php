<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\DataType;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\DataType\Image;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lullabot\Mpx\DataService\DataType\Image
 */
class ImageTest extends TestCase
{
    /**
     * Tests all basic get / set properties.
     *
     * @param string $property
     * @param string $value
     * @dataProvider getSetMethodDataProvider
     */
    public function testGetSet($property, $value)
    {
        $get = 'get'.ucfirst($property);
        $set = 'set'.ucfirst($property);
        $image = new Image();
        $image->$set($value);
        $this->assertSame($value, $image->$get());
    }

    public function testOptionalAnchorHref()
    {
        $image = new Image();
        $this->assertEmpty((string) $image->getAnchorHref());
    }

    public function getSetMethodDataProvider()
    {
        return [
            'href' => [
                'href',
                new Uri('http://www.example.com'),
            ],
            'target' => [
                'target',
                '_blank',
            ],
            'title' => [
                'title',
                'link title',
            ],
            'mime type' => [
                'type',
                'text/html',
            ],
            'anchor href' => [
                'anchorHref',
                new Uri('http://www.example.com'),
            ],
        ];
    }
}
