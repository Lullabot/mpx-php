<?php

namespace Lullabot\Mpx\DataService;

/**
 * An ObjectList represents a list of data service objects from a search query or a notification.
 *
 * @see https://docs.theplatform.com/help/wsf-retrieving-data-objects#tp-toc11
 */
class ObjectList
{
    /**
     * An array of namespaces in the results.
     *
     * @var string[]
     */
    protected $xmlNs;

    /**
     * The start index of this result list.
     *
     * @var int
     */
    protected $startIndex;

    /**
     * The number of items per page in this result set.
     *
     * @var int
     */
    protected $itemsPerPage;

    /**
     * The total number of entries.
     *
     * @var int
     */
    protected $entryCount;

    /**
     * @var object[]
     */
    protected $entries;

    /**
     * @return string[]
     */
    public function getXmlNs(): array
    {
        return $this->xmlNs;
    }

    /**
     * @param string[] $xmlNs
     */
    public function setXmlNs(array $xmlNs)
    {
        $this->xmlNs = $xmlNs;
    }

    /**
     * @return int
     */
    public function getStartIndex(): int
    {
        return $this->startIndex;
    }

    /**
     * @param int $startIndex
     */
    public function setStartIndex(int $startIndex)
    {
        $this->startIndex = $startIndex;
    }

    /**
     * @return int
     */
    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    /**
     * @param int $itemsPerPage
     */
    public function setItemsPerPage(int $itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * @return int
     */
    public function getEntryCount(): int
    {
        return $this->entryCount;
    }

    /**
     * @param int $entryCount
     */
    public function setEntryCount(int $entryCount)
    {
        $this->entryCount = $entryCount;
    }

    /**
     * @return object[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * @param object[] $entries
     */
    public function setEntries(array $entries)
    {
        $this->entries = $entries;
    }

    public function next()
    {
        // Retrieve the next page of results.
    }
}
