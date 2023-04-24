<?php

namespace Lullabot\Mpx\DataService;

use Lullabot\Mpx\DataService\Annotation\CustomField;

/**
 * Class representing a discovered custom field class.
 */
class DiscoveredCustomField
{
    /**
     * DiscoveredCustomField constructor.
     *
     * @param string      $class      The fully-qualified class name of the discovered class.
     * @param CustomField $annotation The annotation object attached to the class.
     */
    public function __construct(private readonly string $class, private readonly CustomField $annotation)
    {
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
