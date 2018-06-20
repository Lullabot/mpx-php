<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\DateTime;

use Lullabot\Mpx\DataService\DateTime\ConcreteDateTime;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass  \Lullabot\Mpx\DataService\DateTime\ConcreteDateTime
 */
class ConcreteDateTimeTest extends TestCase
{
    /**
     * @covers ::fromString
     */
    public function testFromString()
    {
        $date = ConcreteDateTime::fromString('now');
        $this->assertInstanceOf(\DateTime::class, $date->getDateTime());
    }

    /**
     * @covers ::__construct
     * @covers ::format
     */
    public function testFormat()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\DateTime $dateTime */
        $dateTime = $this->createMock(\DateTime::class);
        $dateTime->expects($this->once())->method('format')
            ->with('Y')
            ->willReturn('2007');
        $date = new ConcreteDateTime($dateTime);
        $date->format('Y');
    }

    /**
     * @covers ::getDateTime
     */
    public function testGetDateTime()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\DateTime $dateTime */
        $dateTime = new \DateTime();
        $date = new ConcreteDateTime($dateTime);
        $this->assertSame($dateTime, $date->getDateTime());
    }
}
