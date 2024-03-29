<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use Lullabot\Mpx\Encoder\CJsonEncoder;
use Lullabot\Mpx\Normalizer\UnixMillisecondNormalizer;
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

        unset($tests['customFields']);
        unset($tests['json']);

        return $tests;
    }

    protected function loadFixture($fixture, PropertyTypeExtractorInterface $propertyTypeExtractor)
    {
        $encoders = [new CJsonEncoder()];

        // Attempt normalizing each key in this order, including denormalizing recursively.
        $normalizers = [
            new UnixMillisecondNormalizer(),
            new UriNormalizer(),
            new ObjectNormalizer(null, null, null, $propertyTypeExtractor),
            new ArrayDenormalizer(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);

        $data = file_get_contents(__DIR__."/../../../fixtures/$fixture");
        $this->decoded = \GuzzleHttp\Utils::jsonDecode($data, true);
    }

    protected function assertObjectClass($class, string $field, $expected)
    {
        $object = $this->deserialize($class, $field);

        $method = 'get'.ucfirst($field);
        if (!$expected) {
            $expected = $this->decoded[$field];
        }

        if ('' === $expected) {
            $this->assertEmpty((string) $object->$method());
        } else {
            $this->assertEquals($expected, $object->$method());
        }
    }

    /**
     * @return object
     */
    protected function deserialize($class, string $field)
    {
        // This significantly improves test performance as we only deserialize a single field at a time.
        $filtered = [
            $field => $this->decoded[$field],
        ];

        $data = json_encode($filtered);

        $object = $this->serializer->deserialize($data, $class, 'json');

        return $object;
    }
}
