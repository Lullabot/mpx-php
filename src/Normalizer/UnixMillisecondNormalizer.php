<?php

namespace Lullabot\Mpx\Normalizer;

use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

/**
 * Normalize a millisecond timestamp into a date object.
 */
class UnixMillisecondNormalizer extends DateTimeNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (!is_int($data)) {
            throw new NotNormalizableValueException('The data is not an integer, you should pass an integer representing the unix time in milliseconds.');
        }

        $seconds = floor($data / 1000);
        $remainder = $data % 1000;
        $bySeconds = "$seconds.$remainder";

        $context[self::FORMAT_KEY] = 'U.u';

        return parent::denormalize($bySeconds, $class, $format, $context);
    }
}
