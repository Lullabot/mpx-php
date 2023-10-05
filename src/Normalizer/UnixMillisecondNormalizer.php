<?php

namespace Lullabot\Mpx\Normalizer;

use Lullabot\Mpx\DataService\DateTime\ConcreteDateTime;
use Lullabot\Mpx\DataService\DateTime\ConcreteDateTimeInterface;
use Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Denormalize a millisecond timestamp into a date object.
 *
 * Note this doesn't extend the
 * \Symfony\Component\Serializer\Normalizer\DateTimeNormalizer normalizer
 * directly because the ::denormalize() method in that class is typehinted to
 * \DateTimeInterface and we want to return a ConcreteDateTime object instead.
 * Unfortunately \DateTimeInterface can't be extended by user classes, so the
 * option we have is to unfortunately copy here most methods from
 * DateTimeNormalizer, and modify the ones that are particular to us here.
 */
class UnixMillisecondNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    public const FORMAT_KEY = 'datetime_format';
    public const TIMEZONE_KEY = 'datetime_timezone';

    private array $defaultContext = [
        self::FORMAT_KEY => \DateTime::RFC3339,
        self::TIMEZONE_KEY => null,
    ];

    private static array $supportedTypes = [
        DateTimeFormatInterface::class => true,
        ConcreteDateTimeInterface::class => true,
        ConcreteDateTime::class => true,
    ];

    public function __construct(array $defaultContext = [])
    {
        $this->setDefaultContext($defaultContext);
    }

    public function setDefaultContext(array $defaultContext): void
    {
        $this->defaultContext = array_merge($this->defaultContext, $defaultContext);
    }

    public function getSupportedTypes(?string $format): array {
        return self::$supportedTypes;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function normalize(mixed $object, string $format = null, array $context = []): string
    {
        return '';
    }

    public function supportsNormalization(mixed $data, string $format = NULL): bool
    {
        return FALSE;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): ConcreteDateTime
    {
        if (!\is_int($data)) {
            throw new NotNormalizableValueException('The data is not an integer, you should pass an integer representing the unix time in milliseconds.');
        }

        $seconds = floor($data / 1000);
        $remainder = $data % 1000;
        $bySeconds = "$seconds.$remainder";

        $context[self::FORMAT_KEY] = 'U.u';

        $date = $this->dateNormalizerDenormalize($bySeconds, \DateTime::class, $format, $context);

        return new ConcreteDateTime($date);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return isset(self::$supportedTypes[$type]);
    }

    /**
     * This is a clone of DateTimeNormalizer::denormalize().
     *
     * @param mixed $data
     *   Data to restore.
     * @param string $type
     *   The expected class to instantiate.
     * @param null|string $format
     *   Format the given data was extracted from.
     * @param array $context
     *   Options available to the denormalizer.
     *
     * @return void
     */
    private function dateNormalizerDenormalize(mixed $data, string $type, string $format = null, array $context = []): mixed {
        $dateTimeFormat = $context[self::FORMAT_KEY] ?? null;
        $timezone = $this->getTimezone($context);

        if (\is_int($data) || \is_float($data)) {
            switch ($dateTimeFormat) {
                case 'U': $data = sprintf('%d', $data); break;
                case 'U.u': $data = sprintf('%.6F', $data); break;
            }
        }

        if (!\is_string($data) || '' === trim($data)) {
            throw NotNormalizableValueException::createForUnexpectedDataType('The data is either not an string, an empty string, or null; you should pass a string that can be parsed with the passed format or a valid DateTime string.', $data, [Type::BUILTIN_TYPE_STRING], $context['deserialization_path'] ?? null, true);
        }

        try {
            if (null !== $dateTimeFormat) {
                $object = \DateTime::class === $type ? \DateTime::createFromFormat($dateTimeFormat, $data, $timezone) : \DateTimeImmutable::createFromFormat($dateTimeFormat, $data, $timezone);

                if (false !== $object) {
                    return $object;
                }

                $dateTimeErrors = \DateTime::class === $type ? \DateTime::getLastErrors() : \DateTimeImmutable::getLastErrors();

                throw NotNormalizableValueException::createForUnexpectedDataType(sprintf('Parsing datetime string "%s" using format "%s" resulted in %d errors: ', $data, $dateTimeFormat, $dateTimeErrors['error_count'])."\n".implode("\n", $this->formatDateTimeErrors($dateTimeErrors['errors'])), $data, [Type::BUILTIN_TYPE_STRING], $context['deserialization_path'] ?? null, true);
            }

            $defaultDateTimeFormat = $this->defaultContext[self::FORMAT_KEY] ?? null;

            if (null !== $defaultDateTimeFormat) {
                $object = \DateTime::class === $type ? \DateTime::createFromFormat($defaultDateTimeFormat, $data, $timezone) : \DateTimeImmutable::createFromFormat($defaultDateTimeFormat, $data, $timezone);

                if (false !== $object) {
                    return $object;
                }
            }

            return \DateTime::class === $type ? new \DateTime($data, $timezone) : new \DateTimeImmutable($data, $timezone);
        } catch (NotNormalizableValueException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw NotNormalizableValueException::createForUnexpectedDataType($e->getMessage(), $data, [Type::BUILTIN_TYPE_STRING], $context['deserialization_path'] ?? null, false, $e->getCode(), $e);
        }
    }

    /**
     * @deprecated since Symfony 6.3, use "getSupportedTypes()" instead
     */
    public function hasCacheableSupportsMethod(): bool
    {
        trigger_deprecation('symfony/serializer', '6.3', 'The "%s()" method is deprecated, implement "%s::getSupportedTypes()" instead.', __METHOD__, get_debug_type($this));

        return __CLASS__ === static::class;
    }

    /**
     * Formats datetime errors.
     *
     * @return string[]
     */
    private function formatDateTimeErrors(array $errors): array
    {
        $formattedErrors = [];

        foreach ($errors as $pos => $message) {
            $formattedErrors[] = sprintf('at position %d: %s', $pos, $message);
        }

        return $formattedErrors;
    }

    private function getTimezone(array $context): ?\DateTimeZone
    {
        $dateTimeZone = $context[self::TIMEZONE_KEY] ?? $this->defaultContext[self::TIMEZONE_KEY];

        if (null === $dateTimeZone) {
            return null;
        }

        return $dateTimeZone instanceof \DateTimeZone ? $dateTimeZone : new \DateTimeZone($dateTimeZone);
    }
}
