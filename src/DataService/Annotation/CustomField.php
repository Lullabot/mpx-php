<?php

namespace Lullabot\Mpx\DataService\Annotation;

/**
 * @Annotation
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
