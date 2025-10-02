<?php

namespace Lullabot\Mpx\DataService;

use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * An ObjectList represents a list of data service objects from a search query.
 *
 * @see https://docs.theplatform.com/help/wsf-retrieving-data-objects#tp-toc11
 * @see https://docs.theplatform.com/help/wsf-cjson-format#cJSONformat-cJSONobjectlistpayloads
 */
class ObjectList implements \ArrayAccess, \Iterator, JsonInterface
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
     * @var \Lullabot\Mpx\DataService\ObjectInterface[]
     */
    protected $entries = [];

    /**
     * The total count of objects across all pages.
     *
     * @var int
     */
    protected $totalResults = 0;

    /**
     * @var \Lullabot\Mpx\DataService\ObjectListQuery
     */
    protected $objectListQuery;

    /**
     * The position of the array index.
     *
     * @var int
     */
    protected $position = 0;

    /**
     * The factory used to generate the next object list request.
     *
     * @var \Lullabot\Mpx\DataService\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * The original JSON of this object list.
     *
     * @var array
     */
    protected $json;

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

    public function getStartIndex(): int
    {
        return $this->startIndex;
    }

    public function setStartIndex(int $startIndex)
    {
        $this->startIndex = $startIndex;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function setItemsPerPage(int $itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    public function getEntryCount(): int
    {
        return $this->entryCount;
    }

    /**
     * Set the number of entries in the current list.
     */
    public function setEntryCount(int $entryCount)
    {
        $this->entryCount = $entryCount;
    }

    /**
     * @return ObjectInterface[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * @param ObjectInterface[] $entries
     */
    public function setEntries(array $entries)
    {
        $this->entries = $entries;
        $this->rewind();
    }

    /**
     * Return the total results of this list across all pages.
     *
     * @return int The total number of results.
     */
    public function getTotalResults(): int
    {
        return $this->totalResults;
    }

    /**
     * Set the total results of this list across all pages.
     *
     * @param int The total number of results.
     */
    public function setTotalResults(int $totalResults)
    {
        $this->totalResults = $totalResults;
    }

    public function getObjectListQuery(): ObjectListQuery
    {
        if (!isset($this->objectListQuery)) {
            throw new \LogicException('This object list does not have an ObjectListQuery set.');
        }

        return $this->objectListQuery;
    }

    public function setObjectListQuery(ObjectListQuery $byFields)
    {
        $this->objectListQuery = $byFields;
    }

    /**
     * Set the objects needed to generate a next request.
     *
     * @param DataObjectFactory $dataObjectFactory The factory used to load the next ObjectList.
     */
    public function setDataObjectFactory(DataObjectFactory $dataObjectFactory)
    {
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Return if this object list has a next list to load.
     *
     * @return bool True if a next list exists, false otherwise.
     */
    public function hasNext(): bool
    {
        return !empty($this->entries) && ($this->getStartIndex() + $this->getItemsPerPage() - 1 < $this->getTotalResults());
    }

    /**
     * Return the next object list request, if one exists.
     *
     * @see ObjectList::setDataObjectFactory
     *
     * @return PromiseInterface|bool A promise to the next ObjectList, or false if no list exists.
     */
    public function nextList(): PromiseInterface|bool
    {
        if (!$this->hasNext()) {
            return false;
        }

        if (!isset($this->dataObjectFactory)) {
            throw new \LogicException('setDataObjectFactory must be called before calling nextList.');
        }

        if (!isset($this->objectListQuery)) {
            throw new \LogicException('setByFields must be called before calling nextList.');
        }

        $byFields = clone $this->objectListQuery;
        $range = Range::nextRange($this);
        $byFields->setRange($range);

        return $this->dataObjectFactory->selectRequest($byFields);
    }

    /**
     * Yield select requests for all pages of this object list.
     *
     * @return \Generator A generator returning promises to object lists.
     */
    public function yieldLists(): \Generator
    {
        if (!isset($this->dataObjectFactory)) {
            throw new \LogicException('setDataObjectFactory must be called before calling nextList.');
        }

        if (!isset($this->objectListQuery)) {
            throw new \LogicException('setByFields must be called before calling nextList.');
        }

        // We need to yield ourselves first.
        $thisList = new Promise();
        $thisList->resolve($this);
        yield $thisList;

        $ranges = Range::nextRanges($this);
        foreach ($ranges as $range) {
            $byFields = clone $this->objectListQuery;
            $byFields->setRange($range);
            yield $this->dataObjectFactory->selectRequest($byFields);
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->getEntries()[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->getEntries()[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->entries[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->entries[$offset]);
    }

    public function current(): mixed
    {
        return $this->entries[$this->position];
    }

    public function key(): mixed
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->entries[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function setJson(string $json): void
    {
        $this->json = \GuzzleHttp\Utils::jsonDecode($json, true);
    }

    public function getJson(): array
    {
        if (!$this->json) {
            throw new \LogicException('This object has no original JSON representation available');
        }

        return $this->json;
    }
}
