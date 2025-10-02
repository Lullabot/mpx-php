<?php

namespace Lullabot\Mpx\DataService;

use Doctrine\Common\Annotations\Reader;
use Lullabot\Mpx\DataService\Annotation\DataService;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class to discover Data Service implementations.
 */
class DataServiceDiscovery
{
    /**
     * The array of discovered data services.
     *
     * @var \Lullabot\Mpx\DataService\DiscoveredDataService[]
     */
    private array $dataServices = [];

    /**
     * DataServiceDiscovery constructor.
     *
     * @param string $namespace The namespace of the plugins.
     * @param string $directory The directory of the plugins.
     * @param string $rootDir
     */
    public function __construct(
        private $namespace,
        private $directory,
        /*
         * The root directory to discover from, containing the namespace directory.
         */
        private $rootDir,
        /*
         * The class to use for reading annotations.
         */
        private readonly Reader $annotationReader,
        /*
         * The manager used to discover custom field implementations.
         */
        private readonly CustomFieldManager $customFieldManager,
    ) {
    }

    /**
     * Returns all the data services.
     *
     * @return DiscoveredDataService[] An array of all discovered data services, indexed by service name, object type, and schema version.
     */
    public function getDataServices(): array
    {
        if (!$this->dataServices) {
            $this->discoverDataServices();
        }

        return $this->dataServices;
    }

    /**
     * Discovers data services.
     */
    private function discoverDataServices()
    {
        $path = $this->rootDir.'/'.$this->directory;
        $finder = new Finder();
        $finder->files()->in($path);

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder as $file) {
            $class = $this->classForFile($file);
            /* @var \Lullabot\Mpx\DataService\Annotation\DataService $annotation */
            $annotation = $this->annotationReader->getClassAnnotation(new \ReflectionClass($class), DataService::class);
            if (!$annotation) {
                continue;
            }

            // Now that we have a new data service, we need to attach any
            // custom field implementations.
            $customFields = $this->getCustomFields($annotation);
            $this->dataServices[$annotation->getService()][$annotation->getObjectType()][$annotation->getSchemaVersion()] = new DiscoveredDataService($class, $annotation, $customFields);
        }
    }

    /**
     * Given a file path, return the PSR-4 class it should contain.
     *
     * @param SplFileInfo $file The file to generate the class name for.
     *
     * @return string The fully-qualified class name.
     */
    private function classForFile(SplFileInfo $file): string
    {
        $subnamespace = str_replace('/', '\\', $file->getRelativePath());
        if (!empty($subnamespace)) {
            $subnamespace .= '\\';
        }
        $class = $this->namespace.'\\'.$subnamespace.$file->getBasename('.php');

        return $class;
    }

    /**
     * Return the array of custom fields for an annotation.
     *
     * @param DataService $annotation The Data Service annotation being discovered.
     *
     * @return DiscoveredCustomField[] An array of custom field definitions.
     */
    private function getCustomFields(DataService $annotation): array
    {
        $fields = $this->customFieldManager->getCustomFields();
        $customFields = [];
        if (isset($fields[$annotation->getService()][$annotation->getObjectType()])) {
            $customFields = $fields[$annotation->getService()][$annotation->getObjectType()];
        }

        return $customFields;
    }
}
