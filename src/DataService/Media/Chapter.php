<?php

namespace Lullabot\Mpx\DataService\Media;

use GuzzleHttp\Psr7\Uri;

class Chapter
{
    /**
     * The time when this chapter ends.
     *
     * @var int
     */
    protected $endTime;

    /**
     * The time when this chapter starts.
     *
     * @var int
     */
    protected $startTime;

    /**
     * A link to the thumbnail image for this chapter.
     *
     * @var Uri
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
     *
     * @return int
     */
    public function getEndTime(): ?int
    {
        return $this->endTime;
    }

    /**
     * Set the time when this chapter ends.
     *
     * @param int $endTime
     */
    public function setEndTime(?int $endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * Returns the time when this chapter starts.
     *
     * @return int
     */
    public function getStartTime(): ?int
    {
        return $this->startTime;
    }

    /**
     * Set the time when this chapter starts.
     *
     * @param int $startTime
     */
    public function setStartTime(?int $startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * Returns a link to the thumbnail image for this chapter.
     *
     * @return Uri
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
     *
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the title of the chapter.
     *
     * @param string $title
     */
    public function setTitle(?string $title)
    {
        $this->title = $title;
    }
}
