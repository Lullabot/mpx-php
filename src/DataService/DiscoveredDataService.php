<?php

namespace Lullabot\Mpx\DataService;

use Lullabot\Mpx\DataService\Annotation\DataService;

class DiscoveredDataService
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var DataService
     */
    private $annotation;

    /**
     * The array of custom field objects found for this data service.
     *
     * @var DiscoveredCustomField[]
     */
    private $customFields;

    /**
     * DiscoveredDataService constructor.
     *
     * @param string      $class        The class of the discovered data service.
     * @param DataService $annotation   The annotation of the discovered class.
     * @param array       $customFields (optional) The array of custom field classes.
     */
    public function __construct(string $class, DataService $annotation, array $customFields = [])
    {
        $this->class = $class;
        $this->annotation = $annotation;
        $this->customFields = $customFields;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return DataService
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }

    /**
     * @return DiscoveredCustomField[]
     */
    public function getCustomFields(): array
    {
        return $this->customFields;
    }
}
