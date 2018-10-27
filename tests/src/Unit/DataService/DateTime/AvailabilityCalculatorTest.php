<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\DateTime;

use Lullabot\Mpx\DataService\DateTime\AvailabilityCalculator;
use Lullabot\Mpx\DataService\DateTime\ConcreteDateTime;
use Lullabot\Mpx\DataService\Media\Media;
use PHPUnit\Framework\TestCase;

/**
 * Tests calculating media availability.
 *
 * @coversDefaultClass \Lullabot\Mpx\DataService\DateTime\AvailabilityCalculator
 */
class AvailabilityCalculatorTest extends TestCase
{
    public function testIsAvailable()
    {
        $calculator = new AvailabilityCalculator();
        $media = new Media();
        $media->setAvailableDate(new ConcreteDateTime(new \DateTime('1 minute ago')));
        $media->setExpirationDate(new ConcreteDateTime(new \DateTime('+1 minute')));
        $this->assertTrue($calculator->isAvailable($media, new \DateTime()));
        $this->assertFalse($calculator->isExpired($media, new \DateTime()));
    }

    public function testIsExpired()
    {
        $calculator = new AvailabilityCalculator();
        $media = new Media();
        $media->setExpirationDate(new ConcreteDateTime(new \DateTime('1 minute ago')));
        $media->setAvailableDate(new ConcreteDateTime(new \DateTime('+1 minute')));
        $this->assertTrue($calculator->isExpired($media, new \DateTime()));
        $this->assertFalse($calculator->isAvailable($media, new \DateTime()));
    }

    public function testNullIsAvailable()
    {
        $calculator = new AvailabilityCalculator();
        $media = new Media();
        $this->assertTrue($calculator->isAvailable($media, new \DateTime()));
    }

    public function testEpochAvailable()
    {
        $calculator = new AvailabilityCalculator();
        $media = new Media();
        $media->setAvailableDate(new ConcreteDateTime(\DateTime::createFromFormat('U', 0)));
        $media->setExpirationDate(new ConcreteDateTime(new \DateTime('+1 minute')));
        $this->assertTrue($calculator->isAvailable($media, new \DateTime()));
        $this->assertFalse($calculator->isExpired($media, new \DateTime()));
    }

    public function testEpochExpired()
    {
        $calculator = new AvailabilityCalculator();
        $media = new Media();
        $media->setAvailableDate(new ConcreteDateTime(new \DateTime('-1 minute')));
        $media->setExpirationDate(new ConcreteDateTime(\DateTime::createFromFormat('U', 0)));
        $this->assertTrue($calculator->isAvailable($media, new \DateTime()));
        $this->assertFalse($calculator->isExpired($media, new \DateTime()));
    }
}
