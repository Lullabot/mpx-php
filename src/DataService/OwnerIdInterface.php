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
     */
    public function getOwnerId(): UriInterface;

    /**
     * Set the id of the account that owns this object.
     */
    public function setOwnerId(UriInterface $ownerId);
}
