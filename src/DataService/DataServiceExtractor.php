<?php

namespace Lullabot\Mpx\DataService;

use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Exception\LogicException;

/**
 * A property extractor for mpx objects.
 *
 * Unlike normalizers, only a single extractor can be used with an
 * ObjectNormalizer. This class handles all custom extractions, including:
 *   - Handling the 'entries' key in an Object List.
 *   - Detecting custom fields and assigning them to the appropriate class.
 *
 * This extractor relies on custom fields being decoded into their own arrays,
 * with each array key corresponding to the namespace prefix in the response.
 *
 * @see \Lullabot\Mpx\Encoder\CJsonEncoder
 */
class DataServiceExtractor extends CachingPhpDocExtractor
{
    /**
     * The class each entry is, such as \Lullabot\Mpx\DataService\Media\Media.
     *
     * @var string
     */
    protected $class;

    /**
     * The array of custom field namespaces to use for extracting property info.
     *
     * @var DiscoveredCustomField[]
     */
    protected $customFields = [];

    /**
     * The array of custom field namespace prefix mappings.
     *
     * @var array
     */
    protected $xmlns;

    /**
     * Set the class that is being extracted, such as \Lullabot\Mpx\DataService\Media\Media.
     *
     * @param string $class The class being extracted.
     */
    public function setClass(string $class)
    {
        $this->class = $class;
    }

    /**
     * Set the array of discovered custom fields.
     *
     * @param DiscoveredCustomField[] $customFields The array of custom field namespaces to use for extracting property info.
     */
    public function setCustomFields(array $customFields)
    {
        $this->customFields = $customFields;
    }

    /**
     * Set the array of namespace mappings.
     *
     * @param array $xmlns
     */
    public function setNamespaceMapping(array $xmlns)
    {
        $this->xmlns = $xmlns;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes($class, $property, array $context = [])
    {
        // First, check to see if this is a custom field.
        if (isset($this->xmlns[$property])) {
            $ns = $this->xmlns[$property];

            if (!$discoveredCustomField = $this->customFields[$ns]) {
                throw new LogicException(sprintf('No custom field class was found for %s. setCustomFields() must be called before using this extractor.', $ns));
            }

            return [new Type('object', false, $discoveredCustomField->getClass())];
        }

        if ('customFields' == $property) {
            $collectionKeyType = new Type(Type::BUILTIN_TYPE_STRING);
            $collectionValueType = new Type('object', false, CustomFieldInterface::class);

            return [new Type(Type::BUILTIN_TYPE_ARRAY, false, null, true, $collectionKeyType, $collectionValueType)];
        }

        // Early return for normal top-level properties.
        if ('entries' !== $property) {
            return parent::getTypes($class, $property, $context);
        }

        // This is an object list, so handle the 'entries' key.
        if (!isset($this->class)) {
            throw new \UnexpectedValueException('setClass() must be called before using this extractor.');
        }

        $collectionKeyType = new Type(Type::BUILTIN_TYPE_INT);
        $collectionValueType = new Type('object', false, $this->class);

        return [new Type(Type::BUILTIN_TYPE_ARRAY, false, null, true, $collectionKeyType, $collectionValueType)];
    }
}
