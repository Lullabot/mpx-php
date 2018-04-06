<?php

namespace Lullabot\Mpx\DataService\Annotation;

/**
 * @Annotation
 *
 * Each mpx data service exposes one or more objects. We require annotations
 * on object implementations so calling code can discover what services are
 * currently implemented. In general, there should only be one implementation
 * of a given (objectType, service, schema) triple.
 *
 * @todo Mark the required values with @Required
 * @todo Rename to DataServiceObject
 *
 * @see http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/annotations.html
 */
class DataService
{
    /**
     * The name of the service, such as 'Media Data Service'.
     *
     * @var string
     */
    public $service;

    /**
     * The object type in the service, such as 'Media'.
     *
     * @var string
     */
    public $objectType;

    /**
     * The schema version this class implements, such as '1.10'.
     *
     * @var string
     */
    public $schemaVersion;

    /**
     * The base URI of the service, for when the service registry cannot be used.
     *
     * @var string
     */
    public $baseUri;

    /**
     * Is this service only available over HTTP?
     *
     * @var bool
     */
    public $insecure = false;

    /**
     * Return the service of the data object, such as 'Media Data Service'.
     *
     * @param bool $readonly (optional) Set to true to return the read-only name of the service.
     *
     * @return string The name of the service.
     */
    public function getService($readonly = false): string
    {
        if ($readonly) {
            return $this->service.' read-only';
        }

        return $this->service;
    }

    /**
     * Return the relative objectType of the data service, such as '/data/Media'.
     *
     * @see https://docs.theplatform.com/help/wsf-how-to-find-the-url-of-a-service-in-the-service-registry
     *
     * @return string
     */
    public function getObjectType(): string
    {
        return $this->objectType;
    }

    /**
     * Return the schema version this class implements, such as '1.10'.
     *
     * @return string
     */
    public function getSchemaVersion(): string
    {
        return $this->schemaVersion;
    }

    /**
     * @return string
     */
    public function getBaseUri(): string
    {
        return !empty($this->baseUri) ? $this->baseUri : '';
    }

    public function hasBaseUri(): bool
    {
        return (bool) $this->baseUri;
    }
}
