<?php

namespace Lullabot\Mpx\DataService;

use Lullabot\Mpx\DataService\Annotation\CustomField;

/**
 * Class representing a discovered custom field class.
 */
class DiscoveredCustomField
{
    /**
     * The fully-qualified class name of the discovered class.
     *
     * @var string
     */
    private $class;

    /**
     * The annotation object attached to the class.
     *
     * @var CustomField
     */
    private $annotation;

    /**
     * DiscoveredCustomField constructor.
     *
     * @param string      $class      The fully-qualified class name of the discovered class.
     * @param CustomField $annotation The annotation object attached to the class.
     */
    public function __construct(string $class, CustomField $annotation)
    {
        $this->class = $class;
        $this->annotation = $annotation;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return CustomField
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }
}
