<?php

namespace Lullabot\Mpx\Tests\Unit\Normalizer;

use Lullabot\Mpx\Normalizer\UnixMicrosecondNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

/**
 * Test normalizing a microsecond timestamp.
 *
 * @coversDefaultClass \Lullabot\Mpx\Normalizer\UnixMicrosecondNormalizer
 */
class UnixMicrosecondNormalizerTest extends TestCase
{
    /**
     * Test denormalizing a variety of timestamps.
     *
     * @covers ::denormalize
     */
    public function testDenormalize()
    {
        $normalizer = new UnixMicrosecondNormalizer();
        $normalized = $normalizer->denormalize(0, \DateTime::class);
        $this->assertEquals(0, $normalized->format('U'));
        $normalized = $normalizer->denormalize(1521719898123, \DateTime::class);
        $this->assertEquals(1521719898.123, $normalized->format('U.u'));
    }

    /**
     * Test trying to denormalize invalid data.
     *
     * @covers ::denormalize
     */
    public function testInvalidData()
    {
        $normalizer = new UnixMicrosecondNormalizer();
        $this->expectException(NotNormalizableValueException::class);
        $this->expectExceptionMessage('The data is not an integer, you should pass an integer representing the unix time in microseconds.');
        $normalized = $normalizer->denormalize('0', \DateTime::class);
    }
}
