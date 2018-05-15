<?php

namespace Lullabot\Mpx\DataService\Annotation;

/**
 * Annotation definition for custom field classes.
 *
 * All classes with this annotation must implement \Lullabot\Mpx\DataService\CustomFieldInterface.
 *
 * @Annotation
 *
 * @see \Lullabot\Mpx\DataService\CustomFieldInterface
 */
class CustomField
{
    /**
     * The URI of the custom field namespace the class represents.
     *
     * @var string
     */
    public $namespace;

    /**
     * The name of the service the custom fields apply to, such as 'Media Data Service'.
     *
     * @var string
     */
    public $service;

    /**
     * The object type the custom fields are attached to, such as 'Media'.
     *
     * @var string
     */
    public $objectType;
}
