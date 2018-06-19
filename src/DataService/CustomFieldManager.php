<?php

namespace Lullabot\Mpx\DataService;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class CustomFieldManager
{
    /**
     * @var \Lullabot\Mpx\DataService\CustomFieldDiscoveryInterface
     */
    private $discovery;

    /**
     * CustomFieldManager constructor.
     *
     * @param CustomFieldDiscoveryInterface $discovery The class used to discover custom field implementations.
     */
    public function __construct(CustomFieldDiscoveryInterface $discovery)
    {
        $this->discovery = $discovery;
    }

    /**
     * Register our annotations, relative to this file.
     *
     * @return static
     */
    public static function basicDiscovery(): self
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
     * @return DiscoveredCustomField
     */
    public function getCustomField(string $name, string $objectType, string $namespace): DiscoveredCustomField
    {
        $services = $this->discovery->getCustomFields();
        if (isset($services[$name][$objectType][$namespace])) {
            return $services[$name][$objectType][$namespace];
        }

        throw new \RuntimeException('Custom field not found.');
    }
}
