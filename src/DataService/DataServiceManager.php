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
     * @param CustomFieldManager $customFieldManager (optional) The manager used to discover custom fields.
     *
     * @return static
     */
    public static function basicDiscovery(CustomFieldManager $customFieldManager = null)
    {
        if (!$customFieldManager) {
            $customFieldManager = CustomFieldManager::basicDiscovery();
        }

        // @todo Check Drupal core for other tags to ignore?
        AnnotationReader::addGlobalIgnoredName('class');
        AnnotationRegistry::registerFile(__DIR__.'/Annotation/DataService.php');
        $discovery = new DataServiceDiscovery('\\Lullabot\\Mpx', 'src', __DIR__.'/../..', new AnnotationReader(), $customFieldManager);

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
}
