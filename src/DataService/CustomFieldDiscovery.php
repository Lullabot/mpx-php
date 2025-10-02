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
     * The array of discovered data services.
     *
     * @var \Lullabot\Mpx\DataService\DiscoveredCustomField[]
     */
    private array $customFields = [];

    /**
     * CustomFieldDiscovery constructor.
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
    ) {
    }

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

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
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
        /** @var \Lullabot\Mpx\DataService\Annotation\CustomField $annotation */
        if ($annotation = $this->annotationReader->getClassAnnotation(
            new \ReflectionClass($class),
            CustomField::class
        )) {
            if (!is_subclass_of($class, CustomFieldInterface::class)) {
                throw new \RuntimeException(\sprintf('%s must implement %s.', $class, CustomFieldInterface::class));
            }

            $this->customFields[$annotation->service][$annotation->objectType][$annotation->namespace] = new DiscoveredCustomField(
                $class, $annotation
            );
        }
    }
}
