<?php

namespace Lullabot\Mpx\DataService;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyDescriptionExtractorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\PropertyInfo\Util\PhpDocTypeHelper;

/**
 * Extracts data using a PHPDoc parser, using a cached context factory.
 *
 * @see \Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor
 */
class CachingPhpDocExtractor implements PropertyDescriptionExtractorInterface, PropertyTypeExtractorInterface
{
    final public const PROPERTY = 0;
    final public const ACCESSOR = 1;
    final public const MUTATOR = 2;

    /**
     * @var DocBlock[]
     */
    private array $docBlocks = [];

    private readonly \phpDocumentor\Reflection\DocBlockFactoryInterface|\phpDocumentor\Reflection\DocBlockFactory $docBlockFactory;
    private readonly \Lullabot\Mpx\DataService\CachingContextFactory $contextFactory;
    private readonly \Symfony\Component\PropertyInfo\Util\PhpDocTypeHelper $phpDocTypeHelper;
    private $mutatorPrefixes;
    private $accessorPrefixes;
    private $arrayMutatorPrefixes;

    /**
     * @param DocBlockFactoryInterface $docBlockFactory
     * @param string[]|null            $mutatorPrefixes
     * @param string[]|null            $accessorPrefixes
     * @param string[]|null            $arrayMutatorPrefixes
     */
    public function __construct(DocBlockFactoryInterface $docBlockFactory = null, array $mutatorPrefixes = null, array $accessorPrefixes = null, array $arrayMutatorPrefixes = null)
    {
        if (!class_exists(DocBlockFactory::class)) {
            throw new \RuntimeException(sprintf('Unable to use the "%s" class as the "phpdocumentor/reflection-docblock" package is not installed.', self::class));
        }

        $this->docBlockFactory = $docBlockFactory ?: DocBlockFactory::createInstance();
        $this->contextFactory = new CachingContextFactory();
        $this->phpDocTypeHelper = new PhpDocTypeHelper();
        $this->mutatorPrefixes = $mutatorPrefixes ?? ReflectionExtractor::$defaultMutatorPrefixes;
        $this->accessorPrefixes = $accessorPrefixes ?? ReflectionExtractor::$defaultAccessorPrefixes;
        $this->arrayMutatorPrefixes = $arrayMutatorPrefixes ?? ReflectionExtractor::$defaultArrayMutatorPrefixes;
    }

    /**
     * {@inheritdoc}
     */
    public function getShortDescription($class, $property, array $context = [])
    {
        /** @var $docBlock DocBlock */
        [$docBlock] = $this->getDocBlock($class, $property);
        if (!$docBlock) {
            return;
        }

        $shortDescription = $docBlock->getSummary();

        if (!empty($shortDescription)) {
            return $shortDescription;
        }

        foreach ($docBlock->getTagsByName('var') as $var) {
            if (is_a($var, InvalidTag::class)) {
                throw new \InvalidArgumentException(sprintf('Failed to get the description of the @var tag "%s" for class "%s". Please check that the @var tag is correctly defined.', $property, $class));
            }
            $varDescription = $var->getDescription()->render();

            if (!empty($varDescription)) {
                return $varDescription;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLongDescription($class, $property, array $context = [])
    {
        /** @var $docBlock DocBlock */
        [$docBlock] = $this->getDocBlock($class, $property);
        if (!$docBlock) {
            return;
        }

        $contents = $docBlock->getDescription()->render();

        return '' === $contents ? null : $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes($class, $property, array $context = [])
    {
        /** @var $docBlock DocBlock */
        [$docBlock, $source, $prefix] = $this->getDocBlock($class, $property);
        if (!$docBlock) {
            return;
        }

        switch ($source) {
            case self::PROPERTY:
                $tag = 'var';
                break;

            case self::ACCESSOR:
                $tag = 'return';
                break;

            case self::MUTATOR:
                $tag = 'param';
                break;
        }

        $types = [];
        /** @var DocBlock\Tags\Var_|DocBlock\Tags\Return_|DocBlock\Tags\Param $tag */
        foreach ($docBlock->getTagsByName($tag) as $tag) {
            if (is_a($tag, InvalidTag::class)) {
                return null;
            }
            if ($tag && null !== $tag->getType()) {
                $types = array_merge($types, $this->phpDocTypeHelper->getTypes($tag->getType()));
            }
        }

        if (!isset($types[0])) {
            return;
        }

        if (!\in_array($prefix, $this->arrayMutatorPrefixes)) {
            return $types;
        }

        return [new Type(Type::BUILTIN_TYPE_ARRAY, false, null, true, new Type(Type::BUILTIN_TYPE_INT), $types[0])];
    }

    /**
     * Gets the DocBlock for this property.
     *
     * @param string $class
     * @param string $property
     *
     * @return array
     */
    private function getDocBlock($class, $property)
    {
        $propertyHash = sprintf('%s::%s', $class, $property);

        if (isset($this->docBlocks[$propertyHash])) {
            return $this->docBlocks[$propertyHash];
        }

        $ucFirstProperty = ucfirst($property);

        try {
            switch (true) {
                case $docBlock = $this->getDocBlockFromProperty($class, $property):
                    $data = [$docBlock, self::PROPERTY, null];
                    break;

                case [$docBlock] = $this->getDocBlockFromMethod($class, $ucFirstProperty, self::ACCESSOR):
                    $data = [$docBlock, self::ACCESSOR, null];
                    break;

                case [$docBlock, $prefix] = $this->getDocBlockFromMethod($class, $ucFirstProperty, self::MUTATOR):
                    $data = [$docBlock, self::MUTATOR, $prefix];
                    break;

                default:
                    $data = [null, null, null];
            }
        } catch (\InvalidArgumentException) {
            $data = [null, null, null];
        }

        return $this->docBlocks[$propertyHash] = $data;
    }

    /**
     * Gets the DocBlock from a property.
     *
     * @param string $class
     * @param string $property
     *
     * @return DocBlock|null
     */
    private function getDocBlockFromProperty($class, $property): ?\phpDocumentor\Reflection\DocBlock
    {
        // Use a ReflectionProperty instead of $class to get the parent class if applicable
        try {
            $reflectionProperty = new \ReflectionProperty($class, $property);
        } catch (\ReflectionException) {
            return;
        }

        return $this->docBlockFactory->create($reflectionProperty, $this->contextFactory->createFromReflector($reflectionProperty->getDeclaringClass()));
    }

    /**
     * Gets DocBlock from accessor or mutator method.
     *
     * @param string $class
     * @param string $ucFirstProperty
     * @param int    $type
     *
     * @return array|null
     */
    private function getDocBlockFromMethod($class, $ucFirstProperty, $type): ?array
    {
        $prefixes = self::ACCESSOR === $type ? $this->accessorPrefixes : $this->mutatorPrefixes;
        $prefix = null;

        foreach ($prefixes as $prefix) {
            $methodName = $prefix.$ucFirstProperty;

            try {
                $reflectionMethod = new \ReflectionMethod($class, $methodName);
                if ($reflectionMethod->isStatic()) {
                    continue;
                }

                if (
                    (self::ACCESSOR === $type && 0 === $reflectionMethod->getNumberOfRequiredParameters()) ||
                    (self::MUTATOR === $type && $reflectionMethod->getNumberOfParameters() >= 1)
                ) {
                    break;
                }
            } catch (\ReflectionException) {
                // Try the next prefix if the method doesn't exist
            }
        }

        if (!isset($reflectionMethod)) {
            return;
        }

        return [$this->docBlockFactory->create($reflectionMethod, $this->contextFactory->createFromReflector($reflectionMethod)), $prefix];
    }
}
