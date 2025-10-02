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
     */
    public function setNamespaceMapping(array $xmlns)
    {
        $this->xmlns = $xmlns;
    }

    public function getTypes($class, $property, array $context = [])
    {
        // First, check to see if this object is a custom field.
        if (isset($this->xmlns[$property])) {
            return $this->customFieldInstance($property);
        }

        // Check if this is an array of custom field objects.
        if ('customFields' == $property) {
            return $this->customFieldsArrayType();
        }

        if ('entries' == $property) {
            return $this->entriesType();
        }

        // For all other types rely on the phpdoc extractor.
        return parent::getTypes($class, $property, $context);
    }

    /**
     * Return the type for a custom field class.
     *
     * @param string $prefix The prefix of the namespace.
     */
    private function customFieldInstance($prefix): array
    {
        $ns = $this->xmlns[$prefix];

        if (!$discoveredCustomField = $this->customFields[$ns]) {
            throw new LogicException(\sprintf('No custom field class was found for %s. setCustomFields() must be called before using this extractor.', $ns));
        }

        return [new Type('object', false, $discoveredCustomField->getClass())];
    }

    /**
     * Return the type for an array of custom fields, indexed by a string.
     */
    private function customFieldsArrayType(): array
    {
        $collectionKeyType = new Type(Type::BUILTIN_TYPE_STRING);
        $collectionValueType = new Type('object', false, CustomFieldInterface::class);

        return [new Type(Type::BUILTIN_TYPE_ARRAY, false, null, true, $collectionKeyType, $collectionValueType)];
    }

    /**
     * Return the type of an array of entries used in an object list.
     */
    private function entriesType(): array
    {
        // This is an object list, so handle the 'entries' key.
        if (!isset($this->class)) {
            throw new \UnexpectedValueException('setClass() must be called before using this extractor.');
        }

        $collectionKeyType = new Type(Type::BUILTIN_TYPE_INT);
        $collectionValueType = new Type('object', false, $this->class);

        return [new Type(Type::BUILTIN_TYPE_ARRAY, false, null, true, $collectionKeyType, $collectionValueType)];
    }
}
