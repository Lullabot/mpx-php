<?php

namespace Lullabot\Mpx\DataService;

use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

/**
 * A property extractor to extract the type from a notification entry.
 */
class NotificationTypeExtractor implements PropertyTypeExtractorInterface
{
    /**
     * The class each entry is, such as \Lullabot\Mpx\DataService\Media\Media.
     *
     * @var string
     */
    protected $class;

    /**
     * NotificationTypeReflectionExtractorDecorator constructor.
     *
     * @param \Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface $reflectionExtractor Reflection extractor instance to decorate.
     */
    public function __construct(
        /*
         * Decorated ReflectionExtractor instance.
         */
        protected PropertyTypeExtractorInterface $reflectionExtractor
    ) {
    }

    /**
     * Create a new NotificationTypeExtractor.
     *
     * @return static
     */
    public static function create(): self
    {
        return new static(new ReflectionExtractor());
    }

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
    public function getTypes($class, $property, array $context = []): ?array
    {
        if ('entry' !== $property) {
            return $this->reflectionExtractor->getTypes($class, $property, $context);
        }

        if (!isset($this->class)) {
            throw new \LogicException('setClass() must be called before using this extractor.');
        }

        return [new Type('object', false, $this->class)];
    }
}
