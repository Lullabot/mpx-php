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

    public function __construct(string $class, DataService $annotation)
    {
        $this->class = $class;
        $this->annotation = $annotation;
    }

    /**
     * @return string
     */
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
}
