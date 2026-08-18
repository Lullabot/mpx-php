<?php

namespace Lullabot\Mpx\DataService\Media;

class CategoryInfo implements \Stringable
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
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the Category object's fullTitle value.
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * Returns the Category object's label value.
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Set the Category object's label value.
     */
    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    /**
     * Returns the Category object's scheme value.
     */
    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    /**
     * Set the Category object's scheme value.
     */
    public function setScheme(?string $scheme): void
    {
        $this->scheme = $scheme;
    }

    /**
     * Convert this object the category name.
     */
    public function __toString(): string
    {
        return (string) $this->getName();
    }
}
