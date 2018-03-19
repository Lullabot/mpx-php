<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\Player;

use Lullabot\Mpx\DataService\Player\Player;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Tests the Player object.
 *
 * This class explicitly doesn't use method coverage markers because it would be hundreds of lines.
 *
 * @coversDefaultClass \Lullabot\Mpx\DataService\Player\Player
 */
class PlayerTest extends TestCase
{
    /**
     * The serializer used to decode the data.
     *
     * @var Serializer
     */
    protected $serializer;

    /**
     * The decoded player object fixture.
     *
     * @var array
     */
    protected $decoded;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);

        $data = file_get_contents(__DIR__.'/../../../../fixtures/player-object.json');
        $this->decoded = \GuzzleHttp\json_decode($data, true);
    }

    /**
     * Test get / set methods.
     *
     * @param string $field    The field on Player to test.
     * @param mixed  $expected (optional) Override the assertion if data is converted, such as with timestamps.
     *
     * @dataProvider getSetMethods
     */
    public function testGetSet(string $field, $expected = null)
    {
        // This significantly improves test performance as we only deserialize a single field at a time.
        $filtered = [
            $field => $this->decoded[$field],
        ];

        // @todo This is a total hack as MPX returns JSON with null values. Replace by omitting in the normalizer.
        $filtered = array_filter($filtered, function ($value) {
            return null !== $value;
        });
        $data = json_encode($filtered);

        $player = $this->serializer->deserialize($data, Player::class, 'json');
        $data = json_decode($data, true);

        $method = 'get'.ucfirst($field);
        if (!$expected) {
            $expected = $filtered[$field];
        }
        $this->assertEquals($expected, $player->$method());
    }

    /**
     * Return an array of methods to test.
     *
     * @return array The array of methods to test.
     */
    public function getSetMethods()
    {
        $r = new \ReflectionClass(Player::class);
        foreach ($r->getProperties() as $property) {
            $tests[$property->getName()] = [$property->getName()];
        }
        $tests['added'] = ['added', \DateTime::createFromFormat('U.u', '1335377369.000')];
        $tests['updated'] = ['updated', \DateTime::createFromFormat('U.u', '1335378125.000')];

        return $tests;
    }
}
