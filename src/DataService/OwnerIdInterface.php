<?php

namespace Lullabot\Mpx\DataService;

use Psr\Http\Message\UriInterface;

/**
 * Interface for mpx objects with an owner.
 */
interface OwnerIdInterface
{
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
}
