<?php

namespace Lullabot\Mpx\DataService\Media;

use GuzzleHttp\Psr7\Uri;

class Chapter
{
    /**
     * The time when this chapter ends.
     *
     * @var float
     */
    protected $endTime;

    /**
     * The time when this chapter starts.
     *
     * @var float
     */
    protected $startTime;

    /**
     * A link to the thumbnail image for this chapter.
     *
     * @var \GuzzleHttp\Psr7\Uri
     */
    protected $thumbnailUrl;

    /**
     * The title of the chapter.
     *
     * @var string
     */
    protected $title;

    /**
     * Returns the time when this chapter ends.
     */
    public function getEndTime(): ?float
    {
        return $this->endTime;
    }

    /**
     * Set the time when this chapter ends.
     */
    public function setEndTime(?float $endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * Returns the time when this chapter starts.
     */
    public function getStartTime(): ?float
    {
        return $this->startTime;
    }

    /**
     * Set the time when this chapter starts.
     */
    public function setStartTime(?float $startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * Returns a link to the thumbnail image for this chapter.
     */
    public function getThumbnailUrl(): Uri
    {
        return $this->thumbnailUrl;
    }

    /**
     * Set a link to the thumbnail image for this chapter.
     *
     * @param Uri $thumbnailUrl The URL of the thumbnail.
     */
    public function setThumbnailUrl(Uri $thumbnailUrl)
    {
        $this->thumbnailUrl = $thumbnailUrl;
    }

    /**
     * Returns the title of the chapter.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the title of the chapter.
     */
    public function setTitle(?string $title)
    {
        $this->title = $title;
    }
}
