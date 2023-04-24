<?php

namespace Lullabot\Mpx\Tests\Unit\Normalizer;

use Lullabot\Mpx\Normalizer\UnixMillisecondNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

/**
 * Test normalizing a millisecond timestamp.
 *
 * @coversDefaultClass \Lullabot\Mpx\Normalizer\UnixMillisecondNormalizer
 */
class UnixMillisecondNormalizerTest extends TestCase
{
    /**
     * Test denormalizing a variety of timestamps.
     *
     * @covers ::denormalize
     */
    public function testDenormalize()
    {
        $normalizer = new UnixMillisecondNormalizer();
        $normalized = $normalizer->denormalize(0, \DateTime::class);
        $this->assertEquals(0, $normalized->format('U'));
        $normalized = $normalizer->denormalize(1_521_719_898_123, \DateTime::class);
        $this->assertEquals(1_521_719_898.123, $normalized->format('U.u'));
    }

    /**
     * Test trying to denormalize invalid data.
     *
     * @covers ::denormalize
     */
    public function testInvalidData()
    {
        $normalizer = new UnixMillisecondNormalizer();
        $this->expectException(NotNormalizableValueException::class);
        $this->expectExceptionMessage('The data is not an integer, you should pass an integer representing the unix time in milliseconds.');
        $normalized = $normalizer->denormalize('0', \DateTime::class);
    }
}
