<?php

namespace Lullabot\Mpx\DataService;

use GuzzleHttp\Psr7\Uri;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

/**
 * Extracts URIs with string array keys.
 *
 * Unfortunately, PhpDocExtractor's code uses private variables and final
 * classes, so we can't override it to allow string array keys. Since this API
 * only has one key, it is simpler to to just hardcode the property type.
 *
 * @see \Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor
 */
class ResolveDomainResponseExtractor implements PropertyTypeExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTypes($class, $property, array $context = [])
    {
        if ('resolveDomainResponse' != $property) {
            throw new \InvalidArgumentException('This extractor only supports resolveDomainResponse properties.');
        }

        $collectionKeyType = new Type(Type::BUILTIN_TYPE_STRING);
        $collectionValueType = new Type('object', false, Uri::class);

        return [new Type(Type::BUILTIN_TYPE_ARRAY, false, null, true, $collectionKeyType, $collectionValueType)];
    }
}
