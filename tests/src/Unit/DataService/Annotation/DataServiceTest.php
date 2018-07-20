<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\Annotation;

use Lullabot\Mpx\DataService\Annotation\DataService;
use Lullabot\Mpx\DataService\Field;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lullabot\Mpx\DataService\Annotation\DataService
 */
class DataServiceTest extends TestCase
{
    /**
     * @param $property
     * @param $get
     * @param $value
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($property, $get, $value, $return)
    {
        $annotation = new DataService();
        $annotation->$property = $value;
        $this->assertEquals($return, $annotation->$get());
    }

    public function getSetDataProvider()
    {
        return [
            'object type' => ['objectType', 'getObjectType', '/data/Player', '/data/Player'],
            'schema version' => ['schemaVersion', 'getSchemaVersion', '1.23', '1.23'],
            'base uri' => ['baseUri', 'getBaseUri', 'http://www.example.com/', 'http://www.example.com/'],
            'has base uri' => ['baseUri', 'hasBaseUri', 'http://www.example.com/', true],
            'has empty base uri' => ['baseUri', 'hasBaseUri', '', ''],
            'service' => ['service', 'getService', 'Access Data Service', 'Access Data Service'],
            'path' => ['objectType', 'getPath', 'Media', '/data/Media'],
        ];
    }

    public function testGetFieldDataService()
    {
        $annotation = new DataService();
        $annotation->service = 'Access Data Service';
        $annotation->baseUri = 'http://www.example.com/';
        $discovered = $annotation->getFieldDataService();
        $this->assertEquals(Field::class, $discovered->getClass());
        $discoveredAnnotation = $discovered->getAnnotation();
        $this->assertEquals($annotation->getService(), $discoveredAnnotation->getService());
        $this->assertEquals($annotation->getBaseUri(), $discoveredAnnotation->getBaseUri());
        $this->assertEquals('/Field', $discoveredAnnotation->getObjectType());
        $this->assertEquals('1.2', $discoveredAnnotation->getSchemaVersion());
    }
}
