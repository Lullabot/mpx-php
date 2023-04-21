<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\DataType;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\DataType\Link;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lullabot\Mpx\DataService\DataType\Link
 */
class LinkTest extends TestCase
{
    /**
     * Tests all basic get / set properties.
     *
     * @param string $property
     * @param string $value
     *
     * @dataProvider getSetMethodDataProvider
     */
    public function testGetSet($property, $value)
    {
        $get = 'get'.ucfirst($property);
        $set = 'set'.ucfirst($property);
        $link = new Link();
        $link->$set($value);
        $this->assertSame($value, $link->$get());
    }

    public function testOptionalAnchorHref()
    {
        $link = new Link();
        $this->assertEmpty((string) $link->getHref());
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
        ];
    }
}
