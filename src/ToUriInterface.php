<?php

namespace Lullabot\Mpx;

use Psr\Http\Message\UriInterface;

/**
 * Interface representing classes that can be rendered as a URI.
 */
interface ToUriInterface
{
    /**
     * Return this object as a URI.
     *
     * @return UriInterface
     */
    public function toUri(): UriInterface;

    /**
     * Return this object as a URI string.
     *
     * @return string
     */
    public function __toString();
}
