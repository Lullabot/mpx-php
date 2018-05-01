<?php

namespace Lullabot\Mpx\DataService;

use Psr\Http\Message\UriInterface;

/**
 * Interface for mpx objects with an ID.
 *
 * While all mpx objects have an ID, this interface is separated from
 * ObjectInterface so calling libraries can provide a narrow implementation.
 */
interface IdInterface
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
     * @param UriInterface
     */
    public function setId(UriInterface $id);
}
