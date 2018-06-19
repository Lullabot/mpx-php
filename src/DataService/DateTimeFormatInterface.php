<?php

namespace Lullabot\Mpx\DataService;

/**
 * Represents a date and time that must be formattable as a string.
 *
 * PHP's \DateTime object does not have any way to represent an "empty" date.
 * While there is a \DateTimeInterface class, it's documentation explicitly
 * says it is not for implementation but type hinting only.
 *
 * mpx can return null for most fields, including those that are dates. We
 * don't want calling code to have to check for null returns on every get call.
 * This interface requires that format() return an empty string if the
 * underlying date is not set.
 *
 * @code
 *      $media = new \Lullabot\Mpx\DataService\Media\Media();
 *      $date = $media->getPubDate();
 *      // $formatted will be '' if the media object's published date is undefined.
 *      $formatted = $date->format('Y-m-d H:i:s');
 * @endcode
 */
interface DateTimeFormatInterface
{
    /**
     * Returns date formatted according to given format.
     *
     * @param string $format
     *
     * @return string The formatted date, or an empty string if no date is available.
     *
     * @see http://php.net/manual/en/datetime.format.php
     */
    public function format(string $format): string;
}
