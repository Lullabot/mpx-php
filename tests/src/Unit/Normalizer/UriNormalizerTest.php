<?php

namespace Lullabot\Mpx\Tests\Unit\Normalizer;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\Normalizer\UriNormalizer;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

/**
 * @coversDefaultClass \Lullabot\Mpx\Normalizer\UriNormalizer
 */
class UriNormalizerTest extends TestCase
{
    /**
     * Test normalizing a URI into a string.
     *
     * @covers ::normalize
     */
    public function testNormalize()
    {
        $normalizer = new UriNormalizer();
        $expected = $normalizer->normalize(new Uri('http://example.com'));
        $this->assertInternalType('string', $expected);
        $this->assertEquals('http://example.com', $expected);
    }

    public function testNotUri()
    {
        $normalizer = new UriNormalizer();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The object must implement "\Psr\Http\Message\UriInterface".');
        $normalizer->normalize(new \stdClass());
    }

    /**
     * Test that URIs can be normalized.
     *
     * @covers ::supportsNormalization
     */
    public function testSupportsNormalization()
    {
        $normalizer = new UriNormalizer();
        $this->assertTrue($normalizer->supportsNormalization($this->getMockBuilder(UriInterface::class)->getMock()));
    }

    /**
     * Test denormalizing a string into a URI.
     *
     * @covers ::denormalize
     */
    public function testDenormalize()
    {
        $normalizer = new UriNormalizer();
        $uri = $normalizer->denormalize('http://example.com', UriInterface::class);
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('http://example.com', (string) $uri);
    }

    /**
     * @covers ::denormalize
     */
    public function testEmptyString()
    {
        $normalizer = new UriNormalizer();
        $this->assertInstanceOf(UriInterface::class, $normalizer->denormalize('', UriInterface::class));
        $this->assertInstanceOf(UriInterface::class, $normalizer->denormalize(null, UriInterface::class));
    }

    /**
     * Test that we throw an exception on parse errors.
     *
     * @covers ::denormalize
     */
    public function testParseError()
    {
        $normalizer = new UriNormalizer();
        $this->expectException(\Symfony\Component\Serializer\Exception\NotNormalizableValueException::class);
        $this->expectExceptionMessage('Parsing URI string "http://" resulted in error: Unable to parse URI: http://');
        $normalizer->denormalize('http://', UriInterface::class);
    }

    /**
     * Test that URIs and UriInterfaces can be denormalized.
     *
     * @covers ::supportsDenormalization
     */
    public function testSupportsDenormalization()
    {
        $normalizer = new UriNormalizer();
        $this->assertTrue($normalizer->supportsDenormalization('', Uri::class));
        $this->assertTrue($normalizer->supportsDenormalization('', UriInterface::class));
    }
}
