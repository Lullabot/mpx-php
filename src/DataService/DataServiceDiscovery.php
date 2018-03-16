<?php

namespace Lullabot\Mpx\DataService;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Finder\Finder;

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
     * @var array
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
     */
    public function getDataServices()
    {
        if (!$this->dataServices) {
            $this->discoverDataServices();
        }

        return $this->dataServices;
    }

    /**
     * Discovers data services.
     *
     * @todo Return a structured class?
     */
    private function discoverDataServices()
    {
        $path = $this->rootDir.'/'.$this->directory;
        $finder = new Finder();
        $finder->files()->in($path);

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder as $file) {
            $subnamespace = str_replace('/', '\\', $file->getRelativePath());
            if (!empty($subnamespace)) {
                $subnamespace .= '\\';
            }
            $class = $this->namespace.'\\'.$subnamespace.$file->getBasename('.php');
            $annotation = $this->annotationReader->getClassAnnotation(new \ReflectionClass($class), 'Lullabot\Mpx\DataService\Annotation\DataService');
            if (!$annotation) {
                continue;
            }

            /* @var \Lullabot\Mpx\DataService\Annotation\DataService $annotation */
            $this->dataServices[$annotation->getService()][$annotation->getPath()] = [
                'class' => $class,
                'annotation' => $annotation,
            ];
        }
    }
}
