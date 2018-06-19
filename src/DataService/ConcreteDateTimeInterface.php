<?php

namespace Lullabot\Mpx\DataService;

/**
 * Represents a date that is not empty or null.
 *
 * Typehints supporting empty dates and times should use
 * DateTimeFormatInterface instead of this class. Calling code should then
 * check the value if they need to interact or modify the date / time directly.
 *
 * @code
 *      $media = new \Lullabot\Mpx\DataService\Media\Media();
 *      $date = $media->getPubDate();
 *      if ($date instanceof \Lullabot\Mpx\DataService\ConcreteDateTimeInterface) {
 *          $date->getDateTime()->getTimestamp();
 *      }
 * @endcode
 */
interface ConcreteDateTimeInterface extends DateTimeFormatInterface
{
    /**
     * Return the underlying \DateTime associated with this date.
     *
     * @return \DateTime
     */
    public function getDateTime();
}
