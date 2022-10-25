<?php

namespace Lullabot\Mpx\DataService\DataType;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Represents a Link to an Image.
 *
 * @see https://docs.theplatform.com/help/wsf-index-of-supported-data-types#tp-toc9
 */
class Image extends Link
{
    /**
     * @var \Psr\Http\Message\UriInterface
     */
    protected $anchorHref;

    public function getAnchorHref(): UriInterface
    {
        if (!$this->anchorHref) {
            return new Uri();
        }

        return $this->anchorHref;
    }

    public function setAnchorHref(UriInterface $anchorHref)
    {
        $this->anchorHref = $anchorHref;
    }
}
