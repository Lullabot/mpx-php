<?php

namespace Lullabot\Mpx\DataService;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class to discover Data Service implementations.
 */
class DataServiceDiscovery
{
    /**
     * The namespace to search within, such as '\Lullabot\Mpx'.
     *
     * @var string
     */
    private $namespace;

    /**
     * The root directory of the namespace, such as 'src'.
     *
     * @var string
     */
    private $directory;

    /**
     * The class to use for reading annotations.
     *
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $annotationReader;

    /**
     * The root directory to discover from, containing the namespace directory.
     *
     * @var string
     */
    private $rootDir;

    /**
     * The array of discovered data services.
     *
     * @var DiscoveredDataService[]
     */
    private $dataServices = [];

    /**
     * DataServiceDiscovery constructor.
     *
     * @param string $namespace The namespace of the plugins.
     * @param string $directory The directory of the plugins.
     * @param $rootDir
     * @param \Doctrine\Common\Annotations\Reader $annotationReader
     */
    public function __construct($namespace, $directory, $rootDir, Reader $annotationReader)
    {
        $this->namespace = $namespace;
        $this->annotationReader = $annotationReader;
        $this->directory = $directory;
        $this->rootDir = $rootDir;
    }

    /**
     * Returns all the data services.
     *
     * @return DiscoveredDataService[] An array of all discovered data services.
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

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $class = $this->classForFile($file);
            /* @var \Lullabot\Mpx\DataService\Annotation\DataService $annotation */
            $annotation = $this->annotationReader->getClassAnnotation(new \ReflectionClass($class), 'Lullabot\Mpx\DataService\Annotation\DataService');
            if (!$annotation) {
                continue;
            }

            $this->dataServices[$annotation->getService()][$annotation->getPath()][$annotation->getSchemaVersion()] = new DiscoveredDataService($class, $annotation);
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
}
