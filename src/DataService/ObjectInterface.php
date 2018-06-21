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
     * @param string $namespace The namespace of the fields to retrieve.
     *
     * @return CustomFieldInterface
     */
    public function getCustomFields(string $namespace);

    /**
     * Set the custom fields attached to this data object.
     *
     * @param CustomFieldInterface[] $customFields The array of custom field implementations, keyed by their namespace.
     */
    public function setCustomFields(array $customFields);
}
