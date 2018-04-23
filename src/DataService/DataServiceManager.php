<?php

namespace Lullabot\Mpx\DataService;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class DataServiceManager
{
    /**
     * @var \Lullabot\Mpx\DataService\DataServiceDiscovery
     */
    private $discovery;

    public function __construct(DataServiceDiscovery $discovery)
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
        AnnotationRegistry::registerFile(__DIR__.'/Annotation/DataService.php');
        $discovery = new DataServiceDiscovery('\\Lullabot\\Mpx', 'src', __DIR__.'/../..', new AnnotationReader());

        return new static($discovery);
    }

    /**
     * Returns a list of available data services.
     *
     * @return DiscoveredDataService[]
     */
    public function getDataServices()
    {
        return $this->discovery->getDataServices();
    }

    /**
     * Returns one data service by service.
     *
     * @param string $name
     * @param string $objectType
     * @param string $schema
     *
     * @return DiscoveredDataService
     */
    public function getDataService(string $name, string $objectType, string $schema)
    {
        $services = $this->discovery->getDataServices();
        if (isset($services[$name][$objectType][$schema])) {
            return $services[$name][$objectType][$schema];
        }

        throw new \RuntimeException('Data service not found.');
    }

    /**
     * Creates a data service.
     *
     * @param string $name
     *
     * @throws \RuntimeException
     *
     * @return ObjectInterface
     */
    public function create($name)
    {
        $services = $this->discovery->getDataServices();
        // @todo This is broken.
        if (array_key_exists($name, $services)) {
            $class = $services[$name]['class'];
            if (!class_exists($class)) {
                throw new \RuntimeException('Data service class does not exist.');
            }

            return new $class();
        }

        throw new \RuntimeException('Data service does not exist.');
    }
}
