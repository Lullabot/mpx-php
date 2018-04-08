<?php

namespace Lullabot\Mpx\DataService\Media;

/**
 * @see https://docs.theplatform.com/help/media-availabilitywindow-object
 */
class AvailabilityWindow
{
    /**
     * The DateTime when this playback availability window begins.
     *
     * @var \DateTime
     */
    protected $targetAvailableDate;

    /**
     * The DateTime when this playback availability window ends.
     *
     * @var \DateTime
     */
    protected $targetExpirationDate;

    /**
     * The playback availability tags used to identify this playback availability window.
     *
     * @var string[]
     */
    protected $targetAvailabilityTags;

    /**
     * Returns the DateTime when this playback availability window begins.
     *
     * @return \DateTime
     */
    public function getTargetAvailableDate(): \DateTime
    {
        return $this->targetAvailableDate;
    }

    /**
     * Set the DateTime when this playback availability window begins.
     *
     * @param \DateTime
     */
    public function setTargetAvailableDate($targetAvailableDate)
    {
        $this->targetAvailableDate = $targetAvailableDate;
    }

    /**
     * Returns the DateTime when this playback availability window ends.
     *
     * @return \DateTime
     */
    public function getTargetExpirationDate(): \DateTime
    {
        return $this->targetExpirationDate;
    }

    /**
     * Set the DateTime when this playback availability window ends.
     *
     * @param \DateTime
     */
    public function setTargetExpirationDate($targetExpirationDate)
    {
        $this->targetExpirationDate = $targetExpirationDate;
    }

    /**
     * Returns the playback availability tags used to identify this playback availability window.
     *
     * @return string[]
     */
    public function getTargetAvailabilityTags(): array
    {
        return $this->targetAvailabilityTags;
    }

    /**
     * Set the playback availability tags used to identify this playback availability window.
     *
     * @param string[]
     */
    public function setTargetAvailabilityTags($targetAvailabilityTags)
    {
        $this->targetAvailabilityTags = $targetAvailabilityTags;
    }
}
