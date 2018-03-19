<?php

namespace Lullabot\Mpx\DataService;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

trait ConversionTrait
{
    /**
     * Convert a Unix timestamp in microseconds to a \DateTime, or returns an existing \DateTime.
     *
     * @param \DateTime|int $microseconds A date object, or the number of microseconds since the epoch.
     *
     * @return \DateTime The converted date.
     */
    protected function convertDateTime($microseconds): \DateTime
    {
        if (is_int($microseconds)) {
            // It's simpler to treat the number a string, since we have to create from a format anyways.
            $bySeconds = substr_replace($microseconds, '.', -3, 0);

            return \DateTime::createFromFormat('U.u', $bySeconds);
        }

        return $microseconds;
    }

    /**
     * Convert a string to a URI, if required.
     *
     * @param UriInterface|string $uri The URI to convert.
     *
     * @return UriInterface The converted URI.
     */
    protected function convertUri($uri): UriInterface
    {
        if (is_string($uri)) {
            return new Uri($uri);
        }

        return $uri;
    }
}
