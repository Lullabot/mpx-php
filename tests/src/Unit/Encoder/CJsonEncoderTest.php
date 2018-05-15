<?php

namespace Lullabot\Mpx\Tests\Unit\Encoder;

use Lullabot\Mpx\Encoder\CJsonEncoder;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Lullabot\Mpx\Encoder\CJsonEncoder
 */
class CJsonEncoderTest extends TestCase
{
    /**
     * Tests decoding custom fields into their own key.
     *
     * @covers ::decode()
     * @covers ::decodeCustomFields()
     */
    public function testDecodeCustomFields()
    {
        $data = [
            '$xmlns' => [
                'prefix1' => 'http://example.com/ns1',
                'prefix2' => 'http://example.com/ns2',
            ],
            'prefix1$kittens' => 'Highly recommended',
            'prefix2$puppies' => 'Also worth considering',
        ];

        $encoder = new CJsonEncoder();
        $decoded = $encoder->decode(json_encode($data), 'json');
        $this->assertEquals(
            [
                [
                    'namespace' => 'http://example.com/ns1',
                    'data' => [
                            'kittens' => 'Highly recommended',
                        ],
                ],
                [
                    'namespace' => 'http://example.com/ns2',
                    'data' => [
                            'puppies' => 'Also worth considering',
                        ],
                ],
            ], $decoded['customFields']);
    }
}
