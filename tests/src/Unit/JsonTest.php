<?php

namespace Lullabot\Mpx\Tests\Unit;

use Lullabot\Mpx\Json;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Lullabot\Mpx\Json
 */
class JsonTest extends TestCase
{
    /**
     * @covers ::decode
     */
    public function testDecode()
    {
        $this->assertEquals(['a' => 1], Json::decode('{"a":1}', true));
        $this->assertEquals(1, Json::decode('{"a":1}')->a);
    }

    /**
     * Test that a stream is decoded, as responses are passed in directly.
     *
     * @covers ::decode
     */
    public function testDecodeStringable()
    {
        $stream = \GuzzleHttp\Psr7\Utils::streamFor('{"a":1}');
        $this->assertEquals(['a' => 1], Json::decode($stream, true));
    }

    /**
     * Test that malformed JSON throws the exception callers expect.
     *
     * @covers ::decode
     */
    public function testDecodeInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('json_decode error: Syntax error');
        Json::decode('{not json');
    }

    /**
     * @covers ::decode
     */
    public function testDecodeDepthExceeded()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('json_decode error: Maximum stack depth exceeded');
        Json::decode('{"a":1}', true, 0);
    }

    /**
     * @covers ::encode
     */
    public function testEncode()
    {
        $this->assertEquals('{"a":1}', Json::encode(['a' => 1]));
    }

    /**
     * @covers ::encode
     */
    public function testEncodeInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('json_encode error:');
        Json::encode(fopen('php://memory', 'r'));
    }
}
