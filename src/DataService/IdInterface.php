<?php

namespace Lullabot\Mpx\DataService;

use Psr\Http\Message\UriInterface;

/**
 * Interface for mpx objects with an ID.
 *
 * While all mpx objects have an ID, this interface is separated from
 * ObjectInterface so calling libraries can use this with objects that have
 * conflicting definitions of an ID. For example, a Drupal configuration entity
 * ID is a machine name, while an mpx Account ID is a URI (which is not a valid
 * machine name).
 */
interface IdInterface
{
    /**
     * Returns the globally unique URI of this object.
     *
     * @return UriInterface
     */
    public function getMpxId(): UriInterface;

    /**
     * Set the globally unique URI of this object.
     *
     * @param UriInterface $id
     */
    public function setMpxId(UriInterface $id);
}
