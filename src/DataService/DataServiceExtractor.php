<?php

namespace Lullabot\Mpx\DataService;

use Symfony\Component\PropertyInfo\Type;

/**
 * A property extractor to allow for a ObjectList to have varying 'entries' data types.
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
     * Set the class that is being extracted, such as \Lullabot\Mpx\DataService\Media\Media.
     *
     * @param string $class The class being extracted.
     */
    public function setClass(string $class)
    {
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes($class, $property, array $context = [])
    {
        if ('entries' !== $property) {
            return parent::getTypes($class, $property, $context);
        }

        if (!isset($this->class)) {
            throw new \UnexpectedValueException('setClass() must be called before using this extractor.');
        }

        $collectionKeyType = new Type(Type::BUILTIN_TYPE_INT);
        $collectionValueType = new Type('object', false, $this->class);

        return [new Type(Type::BUILTIN_TYPE_ARRAY, false, null, true, $collectionKeyType, $collectionValueType)];
    }
}
