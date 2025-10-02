<?php

namespace Lullabot\Mpx\DataService\DataType;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Represents a URL along with a target, title, and mime type.
 *
 * @see https://docs.theplatform.com/help/wsf-index-of-supported-data-types#tp-toc7
 */
class Link
{
    /**
     * @var UriInterface
     */
    protected $href;

    /**
     * @var string
     */
    protected $target;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $type;

    public function getHref(): UriInterface
    {
        if (!$this->href) {
            return new Uri();
        }

        return $this->href;
    }

    public function setHref(UriInterface $href)
    {
        $this->href = $href;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target)
    {
        $this->target = $target;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title)
    {
        $this->title = $title;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type)
    {
        $this->type = $type;
    }
}
