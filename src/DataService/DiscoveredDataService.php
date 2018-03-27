<?php

namespace Lullabot\Mpx\DataService;

class DiscoveredDataService
{

    /**
     * @var string
     */
    private $class;
    private $annotation;

    public function __construct(string $class, $annotation)
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
     * @return mixed
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }
}
