<?php

namespace Lullabot\Mpx\Tests\Unit\Normalizer;

use Lullabot\Mpx\DataService\Annotation\CustomField;
use Lullabot\Mpx\DataService\CustomFieldInterface;
use Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface;
use Lullabot\Mpx\DataService\DiscoveredCustomField;
use Lullabot\Mpx\DataService\Media\Media;
use Lullabot\Mpx\DataService\ObjectBase;
use Lullabot\Mpx\Normalizer\CustomFieldsNormalizer;
use Lullabot\Mpx\Normalizer\MissingCustomFieldsClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Tests denormalizing custom fields.
 *
 * @coversDefaultClass \Lullabot\Mpx\Normalizer\CustomFieldsNormalizer
 */
class CustomFieldsNormalizerTest extends TestCase
{
    /**
     * Tests denormalizing custom data from a single namespace.
     *
     * @covers ::__construct
     * @covers ::denormalize
     */
    public function testDenormalize()
    {
        $data = [
            'namespace' => 'http://example.com/ns1',
            'data' => [
                'kittens' => 'Highly recommended',
            ],
        ];

        $annotation = new CustomField();
        $annotation->namespace = 'http://example.com/ns1';
        $annotation->objectType = 'Media';
        $annotation->service = 'Media Data Service';

        $normalizer = new CustomFieldsNormalizer([
            $annotation->namespace => new DiscoveredCustomField(DummyCustomFields::class, $annotation),
        ]);

        /** @var SerializerInterface|MockObject|DenormalizerInterface $serializer */
        $serializer = $this->getMockBuilder([SerializerInterface::class, DenormalizerInterface::class])
            ->getMock();
        $serializer->expects($this->once())->method('denormalize')
            ->with($data['data'], DummyCustomFields::class)
            ->willReturn(new DummyCustomFields());
        $normalizer->setSerializer($serializer);

        $object = $normalizer->denormalize($data, Media::class, 'json');
        $this->assertInstanceOf(DummyCustomFields::class, $object);
    }

    /**
     * Tests when an appropriate serializer has not been injected.
     *
     * @covers ::denormalize
     */
    public function testNotDeormalizable()
    {
        $annotation = new CustomField();
        $annotation->namespace = 'http://example.com/ns1';
        $annotation->objectType = 'Media';
        $annotation->service = 'Media Data Service';
        $normalizer = new CustomFieldsNormalizer([
            $annotation->namespace => new DiscoveredCustomField(DummyCustomFields::class, $annotation),
        ]);
        $data = [
            'namespace' => 'http://example.com/ns1',
            'data' => [
                'kittens' => 'Highly recommended',
            ],
        ];
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Cannot denormalize class "Lullabot\Mpx\DataService\Media\Media" because injected serializer is not a denormalizer');
        $object = $normalizer->denormalize($data, Media::class, 'json');
    }

    /**
     * Tests when no custom field class is available.
     *
     * @covers ::denormalize
     */
    public function testMissingCustomFieldsClass()
    {
        $normalizer = new CustomFieldsNormalizer([]);
        $data = [
            'namespace' => 'http://example.com/ns1',
            'data' => [
                'kittens' => 'Highly recommended',
            ],
        ];
        $object = $normalizer->denormalize($data, Media::class, 'json');
        $this->assertInstanceOf(MissingCustomFieldsClass::class, $object);
    }

    /**
     * Tests in what cases this is a valid normalizer.
     *
     * @covers ::supportsDenormalization
     */
    public function testSupportsDenormalization()
    {
        $normalizer = new CustomFieldsNormalizer([]);
        $this->assertTrue($normalizer->supportsDenormalization([], CustomFieldInterface::class));
        $this->assertFalse($normalizer->supportsDenormalization([], static::class));
    }
}

class DummyCustomFields extends ObjectBase implements CustomFieldInterface
{
    /**
     * The date and time that this object was created.
     *
     * @var DateTimeFormatInterface
     */
    protected $added;

    /**
     * The id of the user that created this object.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $addedByUserId;

    /**
     * The globally unique URI of this object.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $id;

    /**
     * The id of the account that owns this object.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $ownerId;

    /**
     * {@inheritdoc}
     */
    public function getAdded(): DateTimeFormatInterface
    {
        return $this->added;
    }

    /**
     * {@inheritdoc}
     */
    public function setAdded(DateTimeFormatInterface $added)
    {
        $this->added = $added;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddedByUserId(): \Psr\Http\Message\UriInterface
    {
        return $this->addedByUserId;
    }

    /**
     * {@inheritdoc}
     */
    public function setAddedByUserId(UriInterface $addedByUserId)
    {
        $this->addedByUserId = $addedByUserId;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): \Psr\Http\Message\UriInterface
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId(UriInterface $id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwnerId(): \Psr\Http\Message\UriInterface
    {
        return $this->ownerId;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwnerId(UriInterface $ownerId)
    {
        $this->ownerId = $ownerId;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): ?string
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(?string $description)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getGuid(): ?string
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setGuid(?string $guid)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getLocked(): ?bool
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setLocked(?bool $locked)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): ?string
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle(?string $title)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdated(): DateTimeFormatInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdated(DateTimeFormatInterface $updated)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedByUserId(): \Psr\Http\Message\UriInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedByUserId(\Psr\Http\Message\UriInterface $updatedByUserId)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(): ?int
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion(?int $version)
    {
    }
}
