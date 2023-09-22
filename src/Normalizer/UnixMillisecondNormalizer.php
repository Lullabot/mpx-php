<?php

namespace Lullabot\Mpx\Normalizer;

use Lullabot\Mpx\DataService\DateTime\ConcreteDateTime;
use Lullabot\Mpx\DataService\DateTime\ConcreteDateTimeInterface;
use Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

/**
 * Denormalize a millisecond timestamp into a date object.
 */
class UnixMillisecondNormalizer extends DateTimeNormalizer
{
    private static array $supportedTypes = [
        DateTimeFormatInterface::class => true,
        ConcreteDateTimeInterface::class => true,
        ConcreteDateTime::class => true,
    ];

    /**
     * {@inheritdoc}
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): \DateTimeInterface
    {
        if (!\is_int($data)) {
            throw new NotNormalizableValueException('The data is not an integer, you should pass an integer representing the unix time in milliseconds.');
        }

        $seconds = floor($data / 1000);
        $remainder = $data % 1000;
        $bySeconds = "$seconds.$remainder";

        $context[self::FORMAT_KEY] = 'U.u';

        $date = parent::denormalize($bySeconds, \DateTime::class, $format, $context);

        return new ConcreteDateTime($date);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return isset(self::$supportedTypes[$type]);
    }
}
