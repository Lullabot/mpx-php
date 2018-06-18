<?php

namespace Lullabot\Mpx\DataService\Media;

use Lullabot\Mpx\DataService\NullDateTime;

/**
 * @see https://docs.theplatform.com/help/media-availabilitywindow-object
 */
class AvailabilityWindow
{
    /**
     * The DateTime when this playback availability window begins.
     *
     * @var \Lullabot\Mpx\DataService\DateTimeFormatInterface
     */
    protected $targetAvailableDate;

    /**
     * The DateTime when this playback availability window ends.
     *
     * @var \Lullabot\Mpx\DataService\DateTimeFormatInterface
     */
    protected $targetExpirationDate;

    /**
     * The playback availability tags used to identify this playback availability window.
     *
     * @var string[]
     */
    protected $targetAvailabilityTags = [];

    /**
     * Returns the DateTime when this playback availability window begins.
     *
     * @return \Lullabot\Mpx\DataService\DateTimeFormatInterface
     */
    public function getTargetAvailableDate(): \Lullabot\Mpx\DataService\DateTimeFormatInterface
    {
        if (!$this->targetAvailableDate) {
            return new NullDateTime();
        }

        return $this->targetAvailableDate;
    }

    /**
     * Set the DateTime when this playback availability window begins.
     *
     * @param \Lullabot\Mpx\DataService\DateTimeFormatInterface $targetAvailableDate
     */
    public function setTargetAvailableDate(\Lullabot\Mpx\DataService\DateTimeFormatInterface $targetAvailableDate)
    {
        $this->targetAvailableDate = $targetAvailableDate;
    }

    /**
     * Returns the DateTime when this playback availability window ends.
     *
     * @return \Lullabot\Mpx\DataService\DateTimeFormatInterface
     */
    public function getTargetExpirationDate(): \Lullabot\Mpx\DataService\DateTimeFormatInterface
    {
        if (!$this->targetExpirationDate) {
            return new NullDateTime();
        }

        return $this->targetExpirationDate;
    }

    /**
     * Set the DateTime when this playback availability window ends.
     *
     * @param \Lullabot\Mpx\DataService\DateTimeFormatInterface $targetExpirationDate
     */
    public function setTargetExpirationDate(\Lullabot\Mpx\DataService\DateTimeFormatInterface $targetExpirationDate)
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
     * @param string[] $targetAvailabilityTags
     */
    public function setTargetAvailabilityTags(array $targetAvailabilityTags)
    {
        $this->targetAvailabilityTags = $targetAvailabilityTags;
    }
}
