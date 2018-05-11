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
     * Given an object list, return the ranges for each subsequent page.
     *
     * @param ObjectList $list The list to generate ranges for.
     *
     * @return static[] An array of Range objects.
     */
    public static function nextRanges(ObjectList $list): array
    {
        $ranges = [];

        // The end index of the next list. We do this here in case there are no further ranges to return.
        $endIndex = $list->getStartIndex() + $list->getItemsPerPage() - 1;

        // The last index of the final list.
        $finalEndIndex = $list->getTotalResults();

        while ($endIndex < $finalEndIndex) {
            // The start index of the next list.
            $startIndex = ($startIndex ?? $list->getStartIndex()) + $list->getEntryCount();

            // We need to be sure we never return an end range greater than the total count of objects.
            $endIndex = min($startIndex + $list->getItemsPerPage() - 1, $finalEndIndex);

            $range = new self();
            $range->setStartIndex($startIndex)
                ->setEndIndex($endIndex);
            $ranges[] = $range;
        }

        return $ranges;
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

        // @todo PHP appears to leak memory in both json_decode() and unserialize() with large result sets. Our best
        // guess is that some amount of data causes PHP to allocate larger chunks, and perhaps that class of memory
        // isn't ever a candidate for garbage collection. In general, paging over result sets (like in
        // \Lullabot\Mpx\Tests\Functional\DataService\Media\MediaQueryTest) should use a constant amount of memory per
        // page. 250 comes from tests on macOS with PHP 7.2 - going to 300 results causes an obvious memory leak.
        if ($this->endIndex - $this->startIndex > 250) {
            @trigger_error('PHP may leak memory with large result pages. Consider reducing the number of results per page.', E_USER_DEPRECATED);
        }

        return ['range' => $this->startIndex.'-'.$this->endIndex];
    }
}
