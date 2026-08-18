<?php

namespace Lullabot\Mpx\Normalizer;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizer for URI objects.
 */
class UriNormalizer implements NormalizerInterface, DenormalizerInterface
{
    private static array $supportedTypes = [
        Uri::class => true,
        UriInterface::class => true,
    ];

    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        if (!$data instanceof UriInterface) {
            throw new InvalidArgumentException('The object must implement "\\Psr\\Http\\Message\\UriInterface".');
        }

        return (string) $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof UriInterface;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        // If an empty string is normalized, we can still return a valid URI object.
        if ('' === $data || null === $data) {
            return new Uri();
        }

        try {
            $object = new Uri($data);
        } catch (\InvalidArgumentException $e) {
            throw new NotNormalizableValueException(\sprintf('Parsing URI string "%s" resulted in error: %s', $data, $e->getMessage()), $e->getCode(), $e);
        }

        return $object;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return isset(self::$supportedTypes[$type]);
    }

    public function getSupportedTypes(?string $format): array
    {
        return self::$supportedTypes;
    }
}
