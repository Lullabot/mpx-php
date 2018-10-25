<?php

namespace Lullabot\Mpx\DataService;

use Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface;
use Psr\Http\Message\UriInterface;

/**
 * Defines an interface object properties common to all mpx objects.
 */
interface ObjectInterface extends IdInterface, JsonInterface
{
    /**
     * Returns the globally unique URI of this object.
     *
     * @return UriInterface
     */
    public function getId(): UriInterface;

    /**
     * Set the globally unique URI of this object.
     *
     * @param UriInterface $id
     */
    public function setId(UriInterface $id);

    /**
     * Returns the date and time that this object was created.
     *
     * @return DateTimeFormatInterface
     */
    public function getAdded(): DateTimeFormatInterface;

    /**
     * Set the date and time that this object was created.
     *
     * @param DateTimeFormatInterface
     */
    public function setAdded(DateTimeFormatInterface $added);

    /**
     * Returns the id of the user that created this object.
     *
     * @return UriInterface
     */
    public function getAddedByUserId(): UriInterface;

    /**
     * Set the id of the user that created this object.
     *
     * @param UriInterface $addedByUserId
     */
    public function setAddedByUserId(UriInterface $addedByUserId);

    /**
     * Returns the id of the account that owns this object.
     *
     * @return UriInterface
     */
    public function getOwnerId(): UriInterface;

    /**
     * Set the id of the account that owns this object.
     *
     * @param UriInterface $ownerId
     */
    public function setOwnerId(UriInterface $ownerId);

    /**
     * Return custom fields attached to this object.
     *
     * @return CustomFieldInterface[]
     */
    public function getCustomFields();

    /**
     * Set the custom fields attached to this data object.
     *
     * @param CustomFieldInterface[] $customFields The array of custom field implementations, keyed by their namespace.
     */
    public function setCustomFields(array $customFields);

    /**
     * Returns the description of this object.
     *
     * @return string
     */
    public function getDescription(): ?string;

    /**
     * Set the description of this object.
     *
     * @param string $description
     */
    public function setDescription(?string $description);

    /**
     * Returns an alternate identifier for this object that is unique within the owning account.
     *
     * @return string
     */
    public function getGuid(): ?string;

    /**
     * Set an alternate identifier for this object that is unique within the owning account.
     *
     * @param string $guid
     */
    public function setGuid(?string $guid);

    /**
     * Returns whether this object currently allows updates.
     *
     * @return bool
     */
    public function getLocked(): ?bool;

    /**
     * Set whether this object currently allows updates.
     *
     * @param bool $locked
     */
    public function setLocked(?bool $locked);

    /**
     * Returns the name of this object.
     *
     * @return string
     */
    public function getTitle(): ?string;

    /**
     * Set the name of this object.
     *
     * @param string $title
     */
    public function setTitle(?string $title);

    /**
     * Returns the date and time this object was last modified.
     *
     * @return \Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface
     */
    public function getUpdated(): DateTimeFormatInterface;

    /**
     * Set the date and time this object was last modified.
     *
     * @param \Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface $updated
     */
    public function setUpdated(DateTimeFormatInterface $updated);

    /**
     * Returns the id of the user that last modified this object.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getUpdatedByUserId(): \Psr\Http\Message\UriInterface;

    /**
     * Set the id of the user that last modified this object.
     *
     * @param \Psr\Http\Message\UriInterface $updatedByUserId
     */
    public function setUpdatedByUserId(\Psr\Http\Message\UriInterface $updatedByUserId);

    /**
     * Returns this object's modification version, used for optimistic locking.
     *
     * @return int
     */
    public function getVersion(): ?int;

    /**
     * Set this object's modification version, used for optimistic locking.
     *
     * @param int $version
     */
    public function setVersion(?int $version);

}
