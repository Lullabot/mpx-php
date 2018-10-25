<?php

namespace Lullabot\Mpx\Tests\Unit;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\CustomFieldInterface;
use Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface;
use Lullabot\Mpx\DataService\ObjectBase;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

/**
 * @coversDefaultClass \Lullabot\Mpx\DataService\ObjectBase
 */
class ObjectBaseTest extends TestCase
{
    /**
     * @covers ::setCustomFields
     * @covers ::getCustomFields
     */
    public function testGetCustomFields()
    {
        $o = new DummyObjectBase();
        $customFields = [
            'http://www.example.com/xml' => $this->createMock(CustomFieldInterface::class),
        ];
        $o->setCustomFields($customFields);
        $this->assertEquals($customFields, $o->getCustomFields());
    }

    /**
     * @covers ::setJson
     * @covers ::getJson
     */
    public function testGetJson()
    {
        $o = new DummyObjectBase();
        $o->setJson('{}');
        $this->assertEquals([], $o->getJson());
    }

    /**
     * @covers ::getJson
     */
    public function testGetJsonMissing()
    {
        $o = new DummyObjectBase();
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('This object has no original JSON representation available');
        $o->getJson();
    }

    /**
     * @covers ::setMpxId
     * @covers ::getMpxId
     */
    public function testGetMpxId()
    {
        $o = new DummyObjectBase();
        $id = new Uri('http://www.example.com/1');
        $o->setMpxId($id);
        $this->assertSame($id, $o->getMpxId());
        $this->assertSame($id, $o->getId());
    }
}

class DummyObjectBase extends ObjectBase
{
    protected $id;
    protected $added;
    protected $addedByUserId;
    protected $ownerId;

    public function getId(): UriInterface
    {
        return $this->id;
    }

    public function setId(UriInterface $id)
    {
        $this->id = $id;
    }

    public function getAdded(): DateTimeFormatInterface
    {
        return $this->added;
    }

    public function setAdded(DateTimeFormatInterface $added)
    {
        $this->added = $added;
    }

    public function getAddedByUserId(): UriInterface
    {
        return $this->addedByUserId;
    }

    public function setAddedByUserId(UriInterface $addedByUserId)
    {
        $this->addedByUserId = $addedByUserId;
    }

    /**
     * Returns the id of the account that owns this object.
     *
     * @return UriInterface
     */
    public function getOwnerId(): UriInterface
    {
        return $this->ownerId;
    }

    public function setOwnerId(UriInterface $ownerId)
    {
        $this->ownerId = $ownerId;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): ?string {}

    /**
     * {@inheritdoc}
     */
    public function setDescription(?string $description) {}

    /**
     * {@inheritdoc}
     */
    public function getGuid(): ?string {}

    /**
     * {@inheritdoc}
     */
    public function setGuid(?string $guid) {}

    /**
     * {@inheritdoc}
     */
    public function getLocked(): ?bool {}

    /**
     * {@inheritdoc}
     */
    public function setLocked(?bool $locked) {}

    /**
     * {@inheritdoc}
     */
    public function getTitle(): ?string {}

    /**
     * {@inheritdoc}
     */
    public function setTitle(?string $title) {}

    /**
     * {@inheritdoc}
     */
    public function getUpdated(): DateTimeFormatInterface {}

    /**
     * {@inheritdoc}
     */
    public function setUpdated(DateTimeFormatInterface $updated) {}

    /**
     * {@inheritdoc}
     */
    public function getUpdatedByUserId(): \Psr\Http\Message\UriInterface {}

    /**
     * {@inheritdoc}
     */
    public function setUpdatedByUserId(\Psr\Http\Message\UriInterface $updatedByUserId) {}

    /**
     * {@inheritdoc}
     */
    public function getVersion(): ?int {}

    /**
     * {@inheritdoc}
     */
    public function setVersion(?int $version) {}
}
