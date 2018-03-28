<?php

namespace Lullabot\Mpx\DataService\Annotation;

/**
 * @Annotation
 *
 * Each MPX data service exposes one or more objects. We require annotations
 * on object implementations so calling code can discover what services are
 * currently implemented. In general, there should only be one implementation
 * of a given (path, service, schema) triple.
 *
 * @todo Use symfony validation to assert requirements.
 * @todo Mark the required values with @Required
 * @todo Rename to DataServiceObject
 * @todo Can we infer /data/ from path and assume it's always consistent?
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
     * The relative path of the data service, such as '/data/Media'.
     *
     * @var string
     */
    public $path;

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
     * Return the relative path of the data service, such as '/data/Media'.
     *
     * @see https://docs.theplatform.com/help/wsf-how-to-find-the-url-of-a-service-in-the-service-registry
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
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
