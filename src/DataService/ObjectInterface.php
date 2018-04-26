<?php

namespace Lullabot\Mpx\DataService;

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
     * @return \Psr\Http\Message\UriInterface
     */
    public function getAddedByUserId(): \Psr\Http\Message\UriInterface;

    /**
     * Set the id of the user that created this object.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setAddedByUserId($addedByUserId);

    /**
     * Returns the id of the account that owns this object.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getOwnerId(): \Psr\Http\Message\UriInterface;

    /**
     * Set the id of the account that owns this object.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setOwnerId($ownerId);
}
