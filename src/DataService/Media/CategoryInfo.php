<?php

namespace Lullabot\Mpx\DataService\Media;

class CategoryInfo
{
    /**
     * The Category object's fullTitle value.
     *
     * @var string
     */
    protected $name;

    /**
     * The Category object's label value.
     *
     * @var string
     */
    protected $label;

    /**
     * The Category object's scheme value.
     *
     * @var string
     */
    protected $scheme;

    /**
     * Returns the Category object's fullTitle value.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the Category object's fullTitle value.
     *
     * @param string
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the Category object's label value.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Set the Category object's label value.
     *
     * @param string
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Returns the Category object's scheme value.
     *
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Set the Category object's scheme value.
     *
     * @param string
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }
}
