<?php

namespace Lullabot\Mpx\DataService\Feeds;

/**
 * The SortKey object is a dependent object that stores information about the sorting criteria for items in a feed.
 *
 * @see https://docs.theplatform.com/help/feeds-sortkey-object
 */
class SortKey
{
    /**
     * The name of the field on which to sort.
     *
     * @var string
     */
    protected $field;

    /**
     * Whether the objects are sorted in descending order.
     *
     * @var bool
     */
    protected $descending;

    /**
     * Get the name of the field on which to sort.
     *
     * @return string
     */
    public function getField(): ?string
    {
        return $this->field;
    }

    /**
     * Set the name of the field on which to sort.
     *
     * @param string $field
     */
    public function setField(?string $field): void
    {
        $this->field = $field;
    }

    /**
     * Get whether the objects are sorted in descending order.
     *
     * @return bool
     */
    public function getDescending(): ?bool
    {
        return $this->descending;
    }

    /**
     * Set whether the objects are sorted in descending order.
     *
     * @param bool $descending
     */
    public function setDescending(?bool $descending): void
    {
        $this->descending = $descending;
    }
}
