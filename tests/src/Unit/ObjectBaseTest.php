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
    public function testGetCustomFields(): void
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
    public function testGetJson(): void
    {
        $o = new DummyObjectBase();
        $o->setJson('{}');
        $this->assertEquals([], $o->getJson());
    }

    /**
     * @covers ::getJson
     */
    public function testGetJsonMissing(): void
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
    public function testGetMpxId(): void
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

    public function setId(UriInterface $id): void
    {
        $this->id = $id;
    }

    public function getAdded(): DateTimeFormatInterface
    {
        return $this->added;
    }

    public function setAdded(DateTimeFormatInterface $added): void
    {
        $this->added = $added;
    }

    public function getAddedByUserId(): UriInterface
    {
        return $this->addedByUserId;
    }

    public function setAddedByUserId(UriInterface $addedByUserId): void
    {
        $this->addedByUserId = $addedByUserId;
    }

    /**
     * Returns the id of the account that owns this object.
     */
    public function getOwnerId(): UriInterface
    {
        return $this->ownerId;
    }

    public function setOwnerId(UriInterface $ownerId): void
    {
        $this->ownerId = $ownerId;
    }

    public function getDescription(): ?string
    {
    }

    public function setDescription(?string $description): void
    {
    }

    public function getGuid(): ?string
    {
    }

    public function setGuid(?string $guid): void
    {
    }

    public function getLocked(): ?bool
    {
    }

    public function setLocked(?bool $locked): void
    {
    }

    public function getTitle(): ?string
    {
    }

    public function setTitle(?string $title): void
    {
    }

    public function getUpdated(): DateTimeFormatInterface
    {
    }

    public function setUpdated(DateTimeFormatInterface $updated): void
    {
    }

    public function getUpdatedByUserId(): UriInterface
    {
    }

    public function setUpdatedByUserId(UriInterface $updatedByUserId): void
    {
    }

    public function getVersion(): ?int
    {
    }

    public function setVersion(?int $version): void
    {
    }
}
