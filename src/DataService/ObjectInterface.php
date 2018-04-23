<?php

namespace Lullabot\Mpx\DataService;

/**
 * Defines an interface object properties common to all mpx objects.
 */
interface ObjectInterface
{
    /**
     * Returns The date and time that this object was created.
     *
     * @return \DateTime
     */
    public function getAdded(): \DateTime;

    /**
     * Set The date and time that this object was created.
     *
     * @param \DateTime
     */
    public function setAdded($added);

    /**
     * Returns The id of the user that created this object.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getAddedByUserId(): \Psr\Http\Message\UriInterface;

    /**
     * Set The id of the user that created this object.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setAddedByUserId($addedByUserId);

    /**
     * Returns The globally unique URI of this object.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getId(): \Psr\Http\Message\UriInterface;

    /**
     * Set The globally unique URI of this object.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setId($id);

    /**
     * Returns The id of the account that owns this object.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getOwnerId(): \Psr\Http\Message\UriInterface;

    /**
     * Set The id of the account that owns this object.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setOwnerId($ownerId);
}
