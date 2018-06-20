<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use Lullabot\Mpx\DataService\Annotation\CustomField;
use Lullabot\Mpx\DataService\Annotation\DataService;
use Lullabot\Mpx\DataService\CustomFieldDiscoveryInterface;
use Lullabot\Mpx\DataService\CustomFieldManager;
use Lullabot\Mpx\DataService\DataServiceDiscovery;
use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\DataService\DiscoveredCustomField;
use Lullabot\Mpx\DataService\DiscoveredDataService;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Lullabot\Mpx\DataService\DataServiceManager
 */
class DataServiceManagerTest extends TestCase
{
    /**
     * Tests getting a single custom field.
     *
     * @covers ::getDataService
     * @covers ::__construct
     */
    public function testGetDataService()
    {
        $expected = [
            'Media Data Service' => [
                'Media' => [
                    '1.10' => new DiscoveredDataService(\stdClass::class, new DataService()),
                ],
            ],
        ];
        /** @var \PHPUnit_Framework_MockObject_MockObject|DataServiceDiscovery $discovery */
        $discovery = $this->createMock(DataServiceDiscovery::class);
        $discovery->expects($this->once())
            ->method('getDataServices')
            ->willReturn($expected);
        $manager = new DataServiceManager($discovery);
        $this->assertEquals($expected['Media Data Service']['Media']['1.10'], $manager->getDataService('Media Data Service', 'Media', '1.10'));
    }

    /**
     * @covers ::getDataService()
     */
    public function testGetDataServiceDoesNotExist()
    {
        $expected = [];
        /** @var \PHPUnit_Framework_MockObject_MockObject|DataServiceDiscovery $discovery */
        $discovery = $this->createMock(DataServiceDiscovery::class);
        $discovery->expects($this->once())
            ->method('getDataServices')
            ->willReturn($expected);
        $manager = new DataServiceManager($discovery);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Data service not found.');
        $manager->getDataService('Media Data Service', 'Media', '1.10');
    }

    /**
     * Tests getting all custom field classes.
     *
     * @covers ::getDataServices()
     */
    public function testGetDataServices()
    {
        $expected = [
            'Media Data Service' => [
                'Media' => [
                    '1.10' => new DiscoveredDataService(\stdClass::class, new DataService()),
                ],
            ],
        ];
        /** @var \PHPUnit_Framework_MockObject_MockObject|DataServiceDiscovery $discovery */
        $discovery = $this->createMock(DataServiceDiscovery::class);
        $discovery->expects($this->once())
            ->method('getDataServices')
            ->willReturn($expected);
        $manager = new DataServiceManager($discovery);
        $this->assertEquals($expected, $manager->getDataServices());
    }

    /**
     * @covers ::basicDiscovery()
     */
    public function testBasicDiscovery()
    {
        $discovery = DataServiceManager::basicDiscovery();
        $services = $discovery->getDataServices();
        $this->assertInstanceOf(DiscoveredDataService::class, $services['Access Data Service']['Account']['1.0']);
        $this->assertInstanceOf(DiscoveredDataService::class, $services['Player Data Service']['Player']['1.6']);
        $this->assertInstanceOf(DiscoveredDataService::class, $services['Media Data Service']['Media']['1.10']);
        $this->assertInstanceOf(DiscoveredDataService::class, $services['Media Data Service']['MediaFile']['1.10']);
        $this->assertInstanceOf(DiscoveredDataService::class, $services['Media Data Service']['Release']['1.10']);
    }
}
