<?php

namespace Lullabot\Mpx\DataService\DataType;

use Psr\Http\Message\UriInterface;

/**
 * Represents a Link to an Image.
 *
 * @see https://docs.theplatform.com/help/wsf-index-of-supported-data-types#tp-toc9
 */
class Image extends Link
{
    /**
     * @var UriInterface
     */
    protected $anchorHref;

    /**
     * @return UriInterface
     */
    public function getAnchorHref(): UriInterface
    {
        return $this->anchorHref;
    }

    /**
     * @param UriInterface $anchorHref
     */
    public function setAnchorHref(UriInterface $anchorHref)
    {
        $this->anchorHref = $anchorHref;
    }
}
