<?php

namespace Lullabot\Mpx\DataService;

use Lullabot\Mpx\DataService\Annotation\DataService;

class DiscoveredDataService
{
    /**
     * DiscoveredDataService constructor.
     *
     * @param string                                            $class        The class of the discovered data service.
     * @param DataService                                       $annotation   The annotation of the discovered class.
     * @param \Lullabot\Mpx\DataService\DiscoveredCustomField[] $customFields (optional) The array of custom field classes.
     */
    public function __construct(
        private readonly string $class,
        private readonly DataService $annotation,
        /*
         * The array of custom field objects found for this data service.
         */
        private readonly array $customFields = []
    ) {
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
