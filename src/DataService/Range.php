<?php

namespace Lullabot\Mpx\DataService;

/**
 * Represents a query range. Note that ranges are indexed from one, not zero.
 *
 * @see https://docs.theplatform.com/help/wsf-controlling-the-contents-of-the-response-payload#tp-toc18
 */
class Range
{
    /**
     * The start index of this range.
     *
     * @var int
     */
    protected $startIndex;

    /**
     * The end index of this range.
     *
     * @var int
     */
    protected $endIndex;

    /**
     * The start index of this range.
     *
     * @param int $startIndex
     *
     * @return self
     */
    public function setStartIndex(int $startIndex): self
    {
        if ($startIndex < 1) {
            throw new \RangeException('The start index must be 1 or greater.');
        }

        $this->startIndex = $startIndex;

        return $this;
    }

    /**
     * Set the end index of this range.
     *
     * @param int $endIndex
     *
     * @return self
     */
    public function setEndIndex($endIndex): self
    {
        if ($endIndex < 1) {
            throw new \RangeException('The end index must be 1 or greater.');
        }

        $this->endIndex = $endIndex;

        return $this;
    }

    /**
     * Given an object list, return the range to load the next page of objects.
     *
     * @param ObjectList $list The list to find the next page for.
     *
     * @return Range A new range.
     */
    public static function nextRange(ObjectList $list): self
    {
        $range = new self();
        $start = $list->getStartIndex() + $list->getEntryCount();
        $range->setStartIndex($start)
            ->setEndIndex($start - 1 + $list->getItemsPerPage());

        return $range;
    }

    /**
     * Return an array of query parameters representing this range.
     *
     * @return array An array with a 'range' key, or an empty array if neither start or end is set.
     */
    public function toQueryParts(): array
    {
        if (empty($this->startIndex) && empty($this->endIndex)) {
            return [];
        }

        return ['range' => $this->startIndex.'-'.$this->endIndex];
    }
}
