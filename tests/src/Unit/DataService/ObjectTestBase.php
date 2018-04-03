<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use Lullabot\Mpx\Encoder\CJsonEncoder;
use Lullabot\Mpx\Normalizer\UnixMicrosecondNormalizer;
use Lullabot\Mpx\Normalizer\UriNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Base class for testing data objects from MPX.
 */
abstract class ObjectTestBase extends TestCase
{
    /**
     * The class to test. All concrete implementations must set this property.
     *
     * @var string
     */
    protected $class;

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
     * Return an array of methods to test.
     *
     * @return array The array of methods to test.
     */
    public function getSetMethods()
    {
        $r = new \ReflectionClass($this->class);
        $tests = [];
        foreach ($r->getProperties() as $property) {
            $tests[$property->getName()] = [$property->getName()];
        }

        return $tests;
    }

    /**
     * @param $fixture
     * @param $propertyTypeExtractor
     */
    protected function loadFixture($fixture, PropertyTypeExtractorInterface $propertyTypeExtractor)
    {
        $encoders = [new CJsonEncoder()];

        // Attempt normalizing each key in this order, including denormalizing recursively.
        $normalizers = [
            new UnixMicrosecondNormalizer(),
            new UriNormalizer(),
            new ObjectNormalizer(null, null, null, $propertyTypeExtractor),
            new ArrayDenormalizer(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);

        $data = file_get_contents(__DIR__."/../../../fixtures/$fixture");
        $this->decoded = \GuzzleHttp\json_decode($data, true);
    }

    /**
     * @param $class
     * @param string $field
     * @param $expected
     */
    protected function assertObjectClass($class, string $field, $expected)
    {
        // This significantly improves test performance as we only deserialize a single field at a time.
        $filtered = [
            $field => $this->decoded[$field],
        ];

        $data = json_encode($filtered);

        $object = $this->serializer->deserialize($data, $class, 'json');

        $method = 'get'.ucfirst($field);
        if (!$expected) {
            $expected = $filtered[$field];
        }

        if ('' === $expected) {
            $this->assertEmpty((string) $object->$method());
        } else {
            $this->assertEquals($expected, $object->$method());
        }
    }
}
