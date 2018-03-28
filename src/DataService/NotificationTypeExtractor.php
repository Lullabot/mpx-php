<?php

namespace Lullabot\Mpx\DataService;

use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\Type;

/**
 * A property extractor to extract the type from a notification entry.
 */
class NotificationTypeExtractor extends ReflectionExtractor
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
        if ('entry' !== $property) {
            return parent::getTypes($class, $property, $context);
        }

        if (!isset($this->class)) {
            throw new \UnexpectedValueException('setClass() must be called before using this extractor.');
        }

        return [new Type('object', false, $this->class)];
    }
}
