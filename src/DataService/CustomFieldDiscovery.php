<?php

namespace Lullabot\Mpx\DataService;

use Doctrine\Common\Annotations\Reader;
use Lullabot\Mpx\DataService\Annotation\CustomField;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class to discover Custom Field implementations.
 */
class CustomFieldDiscovery implements CustomFieldDiscoveryInterface
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
     * @var DiscoveredCustomField[]
     */
    private $customFields = [];

    /**
     * CustomFieldDiscovery constructor.
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
     * {@inheritdoc}
     */
    public function getCustomFields(): array
    {
        if (!$this->customFields) {
            $this->discoverCustomFields();
        }

        return $this->customFields;
    }

    /**
     * Discovers custom fields.
     */
    private function discoverCustomFields()
    {
        $path = $this->rootDir.'/'.$this->directory;
        $finder = new Finder();
        $finder->files()->in($path);

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $class = $this->classForFile($file);
            /* @var \Lullabot\Mpx\DataService\Annotation\CustomField $annotation */
            $this->registerAnnotation($class);
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
     * Register an annotation and it's custom fields.
     *
     * @param string $class The class to inspect for a CustomField annotation.
     */
    private function registerAnnotation($class)
    {
        /** @var CustomField $annotation */
        if ($annotation = $this->annotationReader->getClassAnnotation(
            new \ReflectionClass($class),
            'Lullabot\Mpx\DataService\Annotation\CustomField'
        )) {
            if (!is_subclass_of($class, CustomFieldInterface::class)) {
                throw new \RuntimeException(sprintf('%s must implement %s.', $class, CustomFieldInterface::class));
            }

            $this->customFields[$annotation->service][$annotation->objectType][$annotation->namespace] = new DiscoveredCustomField(
                $class, $annotation
            );
        }
    }
}
