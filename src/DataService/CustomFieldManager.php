<?php

namespace Lullabot\Mpx\DataService;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Lullabot\Mpx\DataService\Annotation\CustomField;

class CustomFieldManager
{
    /**
     * @var \Lullabot\Mpx\DataService\CustomFieldDiscovery
     */
    private $discovery;

    public function __construct(CustomFieldDiscovery $discovery)
    {
        $this->discovery = $discovery;
    }

    /**
     * Register our annotations, relative to this file.
     *
     * @return static
     */
    public static function basicDiscovery()
    {
        // @todo Check Drupal core for other tags to ignore?
        AnnotationReader::addGlobalIgnoredName('class');
        AnnotationRegistry::registerFile(__DIR__.'/Annotation/CustomField.php');
        $discovery = new CustomFieldDiscovery('\\Lullabot\\Mpx', 'src', __DIR__.'/../..', new AnnotationReader());

        return new static($discovery);
    }

    /**
     * Returns a list of available custom fields.
     *
     * @return array
     */
    public function getCustomFields()
    {
        return $this->discovery->getCustomFields();
    }

    /**
     * Returns one custom field.
     *
     * @param string $name
     * @param string $objectType
     * @param string $namespace
     *
     * @return CustomField
     */
    public function getCustomField(string $name, string $objectType, string $namespace)
    {
        $services = $this->discovery->getCustomFields();
        if (isset($services[$name][$objectType][$namespace])) {
            return $services[$name][$objectType][$namespace];
        }

        throw new \RuntimeException('Custom field not found.');
    }
}
