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

    public function normalize($object, $format = null, array $context = [])
    {
        if (!$object instanceof UriInterface) {
            throw new InvalidArgumentException('The object must implement "\\Psr\\Http\\Message\\UriInterface".');
        }

        return (string) $object;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof UriInterface;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        // If an empty string is normalized, we can still return a valid URI object.
        if ('' === $data || null === $data) {
            return new Uri();
        }

        try {
            $object = new Uri($data);
        } catch (\InvalidArgumentException $e) {
            throw new NotNormalizableValueException(sprintf('Parsing URI string "%s" resulted in error: %s', $data, $e->getMessage()), $e->getCode(), $e);
        }

        return $object;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return isset(self::$supportedTypes[$type]);
    }
}
