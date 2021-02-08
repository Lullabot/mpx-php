<?php

namespace Lullabot\Mpx\DataService;

use Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface;
use Psr\Http\Message\UriInterface;

/**
 * Defines an interface object properties common to all mpx objects.
 */
interface ObjectInterface extends IdInterface, GuidInterface, JsonInterface
{
    /**
     * Returns the globally unique URI of this object.
     */
    public function getId(): UriInterface;

    /**
     * Set the globally unique URI of this object.
     */
    public function setId(UriInterface $id);

    /**
     * Returns the date and time that this object was created.
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
     */
    public function getAddedByUserId(): UriInterface;

    /**
     * Set the id of the user that created this object.
     */
    public function setAddedByUserId(UriInterface $addedByUserId);

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
     */
    public function getUpdated(): DateTimeFormatInterface;

    /**
     * Set the date and time this object was last modified.
     */
    public function setUpdated(DateTimeFormatInterface $updated);

    /**
     * Returns the id of the user that last modified this object.
     */
    public function getUpdatedByUserId(): UriInterface;

    /**
     * Set the id of the user that last modified this object.
     */
    public function setUpdatedByUserId(UriInterface $updatedByUserId);

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
