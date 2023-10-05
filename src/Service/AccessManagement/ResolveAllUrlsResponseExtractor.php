<?php

namespace Lullabot\Mpx\Service\AccessManagement;

use GuzzleHttp\Psr7\Uri;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

/**
 * Extracts URIs with string array keys.
 *
 * Unfortunately, PhpDocExtractor's code uses private variables and final
 * classes, so we can't override it to allow string array keys. Since this API
 * only has one key, it is simpler to just hardcode the property type.
 *
 * @see \Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor
 */
class ResolveAllUrlsResponseExtractor implements PropertyTypeExtractorInterface
{
    public function getTypes($class, $property, array $context = [])
    {
        if ('resolveAllUrlsResponse' != $property) {
            throw new \InvalidArgumentException('This extractor only supports resolveAllUrlsResponse properties.');
        }

        $collectionKeyType = new Type(Type::BUILTIN_TYPE_STRING);
        $collectionValueType = new Type('object', false, Uri::class);

        return [new Type(Type::BUILTIN_TYPE_ARRAY, false, null, true, $collectionKeyType, $collectionValueType)];
    }
}
