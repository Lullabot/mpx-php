<?php

namespace Lullabot\Mpx\DataService\DateTime;

use Lullabot\Mpx\DataService\Media\Media;

/**
 * Calculate the availability of an mpx media object.
 *
 * While mpx has an availability state property, we want to be able to use
 * cached mpx data instead of having to re-fetch it from upstream.
 *
 * @todo Support checking availability windows too.
 */
class AvailabilityCalculator
{
    /**
     * Return if the media is available as of the given time.
     *
     * @param $time
     *
     * @return bool
     */
    public function isAvailable(Media $media, \DateTime $time)
    {
        return $this->between($time, $media->getAvailableDate(), $media->getExpirationDate());
    }

    /**
     * Return if the media is expired as of the given time.
     *
     * @param $time
     *
     * @return bool
     */
    public function isExpired(Media $media, \DateTime $time)
    {
        return !$this->isAvailable($media, $time);
    }

    /**
     * Return if a time is between a start and end time.
     *
     * @return bool
     */
    private function between(\DateTime $time, DateTimeFormatInterface $start, DateTimeFormatInterface $end)
    {
        return $this->after($time, $start) && $this->before($time, $end);
    }

    /**
     * Return if a time is equal to or after the other time.
     *
     * @return bool
     */
    private function after(\DateTime $time, DateTimeFormatInterface $other)
    {
        if ($other instanceof ConcreteDateTimeInterface) {
            // If the other time is the Unix epoch, the time should represent
            // the "beginning of all time".
            // @see https://docs.theplatform.com/help/media-media-availabledate
            if ($this->isEpoch($other)) {
                return true;
            }

            return $time >= $other->getDateTime();
        }

        // The other time is a NullDateTime, so we take this to mean the video
        // is available.
        return true;
    }

    /**
     * Return if a given time is the Unix epoch.
     *
     * @return bool
     */
    private function isEpoch(ConcreteDateTimeInterface $concreteDateTime)
    {
        return $concreteDateTime->getDateTime() == \DateTime::createFromFormat('U', 0);
    }

    /**
     * Return if a time is equal to or before the other time.
     *
     * @return bool
     */
    private function before(\DateTime $time, DateTimeFormatInterface $other)
    {
        if ($other instanceof ConcreteDateTimeInterface) {
            // If the other time is the Unix epoch, the time should represent
            // the "end of all time".
            // @see https://docs.theplatform.com/help/media-media-expirationdate
            if ($this->isEpoch($other)) {
                return true;
            }

            return $time <= $other->getDateTime();
        }

        // The other time is a NullDateTime, so we take this to mean the video
        // is available.
        return true;
    }
}
