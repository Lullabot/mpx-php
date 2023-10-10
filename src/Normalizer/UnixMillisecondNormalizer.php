<?php

namespace Lullabot\Mpx\Normalizer;

use Lullabot\Mpx\DataService\DateTime\ConcreteDateTime;
use Lullabot\Mpx\DataService\DateTime\ConcreteDateTimeInterface;
use Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Denormalize a millisecond timestamp into a date object.
 *
 * Note this doesn't extend the
 * \Symfony\Component\Serializer\Normalizer\DateTimeNormalizer normalizer
 * directly because the ::denormalize() method in that class is typehinted to
 * \DateTimeInterface but we want to return a ConcreteDateTime object instead.
 * Because of this, we decorate here a DateTimeNormalizer object and delegate
 * all methods but ::denormalize().
 */
class UnixMillisecondNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{

    /**
     * @var \Symfony\Component\Serializer\Normalizer\DateTimeNormalizer
     */
    private $decorated;

    private static array $supportedTypes = [
        DateTimeFormatInterface::class => true,
        ConcreteDateTimeInterface::class => true,
        ConcreteDateTime::class => true,
    ];

    public function __construct(array $defaultContext = [])
    {
        $this->decorated = new DateTimeNormalizer($defaultContext);
    }

    public function setDefaultContext(array $defaultContext): void
    {
        $this->decorated->setDefaultContext($defaultContext);
    }

    public function getSupportedTypes(?string $format): array
    {
        return self::$supportedTypes;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function normalize(mixed $object, string $format = null, array $context = []): string
    {
        return $this->decorated->normalize($object, $format, $context);
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): ConcreteDateTime
    {
        if (!\is_int($data)) {
            throw new NotNormalizableValueException('The data is not an integer, you should pass an integer representing the unix time in milliseconds.');
        }

        $seconds = floor($data / 1000);
        $remainder = $data % 1000;
        $bySeconds = "$seconds.$remainder";

        $context[$this->decorated::FORMAT_KEY] = 'U.u';

        $date = $this->decorated->denormalize($bySeconds, \DateTime::class, $format, $context);

        return new ConcreteDateTime($date);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return isset(self::$supportedTypes[$type]);
    }

    /**
     * @deprecated since Symfony 6.3, use "getSupportedTypes()" instead
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return $this->decorated->hasCacheableSupportsMethod();
    }

}
