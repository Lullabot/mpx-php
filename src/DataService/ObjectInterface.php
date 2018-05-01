<?php

namespace Lullabot\Mpx\DataService;

use Psr\Http\Message\UriInterface;

/**
 * Defines an interface object properties common to all mpx objects.
 */
interface ObjectInterface extends IdInterface
{
    /**
     * Returns the date and time that this object was created.
     *
     * @return \DateTime
     */
    public function getAdded(): \DateTime;

    /**
     * Set the date and time that this object was created.
     *
     * @param \DateTime
     */
    public function setAdded($added);

    /**
     * Returns the id of the user that created this object.
     *
     * @return UriInterface
     */
    public function getAddedByUserId(): UriInterface;

    /**
     * Set the id of the user that created this object.
     *
     * @param UriInterface
     */
    public function setAddedByUserId($addedByUserId);

    /**
     * Returns the id of the account that owns this object.
     *
     * @return UriInterface
     */
    public function getOwnerId(): UriInterface;

    /**
     * Set the id of the account that owns this object.
     *
     * @param UriInterface
     */
    public function setOwnerId($ownerId);
}
