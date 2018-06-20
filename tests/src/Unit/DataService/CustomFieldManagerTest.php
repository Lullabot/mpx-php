<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use Lullabot\Mpx\DataService\Annotation\CustomField;
use Lullabot\Mpx\DataService\CustomFieldDiscoveryInterface;
use Lullabot\Mpx\DataService\CustomFieldManager;
use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\DataService\DiscoveredCustomField;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Lullabot\Mpx\DataService\CustomFieldManager
 */
class CustomFieldManagerTest extends TestCase
{
    /**
     * Tests getting a single custom field.
     *
     * @covers ::getCustomField
     * @covers ::__construct
     */
    public function testGetCustomField()
    {
        $expected = [
            'Media Data Service' => [
                'Media' => [
                    'http://www.example.com/xml' => new DiscoveredCustomField(\stdClass::class, new CustomField()),
                ],
            ],
        ];
        /** @var \PHPUnit_Framework_MockObject_MockObject|CustomFieldDiscoveryInterface $discovery */
        $discovery = $this->createMock(CustomFieldDiscoveryInterface::class);
        $discovery->expects($this->once())
            ->method('getCustomFields')
            ->willReturn($expected);
        $manager = new CustomFieldManager($discovery);
        $this->assertEquals($expected['Media Data Service']['Media']['http://www.example.com/xml'], $manager->getCustomField('Media Data Service', 'Media', 'http://www.example.com/xml'));
    }

    /**
     * @covers ::getCustomField
     */
    public function testGetCustomFieldDoesNotExist()
    {
        $expected = [];
        /** @var \PHPUnit_Framework_MockObject_MockObject|CustomFieldDiscoveryInterface $discovery */
        $discovery = $this->createMock(CustomFieldDiscoveryInterface::class);
        $discovery->expects($this->once())
            ->method('getCustomFields')
            ->willReturn($expected);
        $manager = new CustomFieldManager($discovery);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Custom field not found.');
        $manager->getCustomField('Media Data Service', 'Media', 'http://www.example.com/xml');
    }

    /**
     * Tests getting all custom field classes.
     *
     * @covers ::getCustomFields
     */
    public function testGetCustomFields()
    {
        $expected = [
            'Media Data Service' => [
                'Media' => [
                    'http://www.example.com/xml' => new DiscoveredCustomField(\stdClass::class, new CustomField()),
                ],
            ],
        ];
        /** @var \PHPUnit_Framework_MockObject_MockObject|CustomFieldDiscoveryInterface $discovery */
        $discovery = $this->createMock(CustomFieldDiscoveryInterface::class);
        $discovery->expects($this->once())
            ->method('getCustomFields')
            ->willReturn($expected);
        $manager = new CustomFieldManager($discovery);
        $this->assertEquals($expected, $manager->getCustomFields());
    }

    /**
     * @covers ::basicDiscovery()
     */
    public function testBasicDiscovery()
    {
        DataServiceManager::basicDiscovery();
        $discovery = CustomFieldManager::basicDiscovery();
        $this->assertEmpty($discovery->getCustomFields());
    }
}
