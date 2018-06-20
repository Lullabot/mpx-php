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
     * @covers ::decode
     * @covers ::decodeCustomFields
     * @covers ::decodeObject
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
                'http://example.com/ns1' => [
                    'namespace' => 'http://example.com/ns1',
                    'data' => [
                        'kittens' => 'Highly recommended',
                    ],
                ],
                'http://example.com/ns2' => [
                    'namespace' => 'http://example.com/ns2',
                    'data' => [
                        'puppies' => 'Also worth considering',
                    ],
                ],
            ], $decoded['customFields']);
    }

    public function testDecodeEntries()
    {
        $data = [
            '$xmlns' => [
                'prefix1' => 'http://example.com/ns1',
                'prefix2' => 'http://example.com/ns2',
            ],
            'entries' => [
                [
                    'prefix1$kittens' => 'Highly recommended',
                    'prefix2$puppies' => 'Also worth considering',
                ],
                [
                    'prefix2$puppies' => 'Highly recommended',
                    'prefix1$kittens' => 'Also worth considering',
                ],
            ],
        ];

        $encoder = new CJsonEncoder();
        $decoded = $encoder->decode(json_encode($data), 'json');
        $this->assertEquals(
            [
                '$xmlns' => [
                    'prefix1' => 'http://example.com/ns1',
                    'prefix2' => 'http://example.com/ns2',
                ],
                'entries' => [
                    0 => [
                        'prefix1$kittens' => 'Highly recommended',
                        'prefix2$puppies' => 'Also worth considering',
                        'customFields' => [
                            'http://example.com/ns1' => [
                                'namespace' => 'http://example.com/ns1',
                                'data' => [
                                    'kittens' => 'Highly recommended',
                                ],
                            ],
                            'http://example.com/ns2' => [
                                'namespace' => 'http://example.com/ns2',
                                'data' => [
                                    'puppies' => 'Also worth considering',
                                ],
                            ],
                        ],
                    ],
                    1 => [
                        'prefix2$puppies' => 'Highly recommended',
                        'prefix1$kittens' => 'Also worth considering',
                        'customFields' => [
                            'http://example.com/ns1' => [
                                'namespace' => 'http://example.com/ns1',
                                'data' => [
                                    'kittens' => 'Also worth considering',
                                ],
                            ],
                            'http://example.com/ns2' => [
                                'namespace' => 'http://example.com/ns2',
                                'data' => [
                                    'puppies' => 'Highly recommended',
                                ],
                            ],
                        ],
                    ],
                ],
            ], $decoded);
    }
}
