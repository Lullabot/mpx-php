<?php

namespace Lullabot\Mpx\DataService\Annotation;

use Lullabot\Mpx\DataService\DiscoveredDataService;
use Lullabot\Mpx\DataService\Field;

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

    /**
     * Return the relative path of this object within the data service.
     *
     * @return string The relative path, such as '/data/Account'.
     */
    public function getPath(): string
    {
        return '/data/'.$this->objectType;
    }

    /**
     * Return a discovered data service for custom fields.
     *
     * Custom fields break the conventions set by all other data services in
     * that they support CRUD operations, but have paths that are the child of
     * another object type. As well, they have schemas, but thePlatform has no
     * documentation of their history. To simplify the implementation, we do
     * not support discoverable classes for field definitions.
     *
     * @return DiscoveredDataService A data service suitable for using with a DataObjectFactory.
     */
    public function getFieldDataService(): DiscoveredDataService
    {
        $service = clone $this;
        $service->objectType .= '/Field';
        $service->schemaVersion = '1.2';

        return new DiscoveredDataService(Field::class, $service);
    }
}
