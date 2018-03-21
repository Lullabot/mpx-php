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
    private static $supportedTypes = [
        Uri::class => true,
        UriInterface::class => true,
    ];

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if (!$object instanceof UriInterface) {
            throw new InvalidArgumentException('The object must implement "\\Psr\\Http\\Message\\UriInterface".');
        }

        return (string) $object;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof UriInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if ('' === $data || null === $data) {
            throw new NotNormalizableValueException('The data is either an empty string or null, you should pass a valid URI string.');
        }

        try {
            $object = new Uri($data);
        } catch (\InvalidArgumentException $e) {
            throw new NotNormalizableValueException(sprintf(
                    'Parsing datetime string "%s" using format "%s" resulted in %d errors:'."\n".'%s'), $e->getCode(), $e);
        }

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return isset(self::$supportedTypes[$type]);
    }
}
