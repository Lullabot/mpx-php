<?php

namespace Lullabot\Mpx\DataService;

use GuzzleHttp\Promise\PromiseInterface;
use Lullabot\Mpx\DataService\Access\Account;

/**
 * An ObjectList represents a list of data service objects from a search query.
 *
 * @see https://docs.theplatform.com/help/wsf-retrieving-data-objects#tp-toc11
 * @see https://docs.theplatform.com/help/wsf-cjson-format#cJSONformat-cJSONobjectlistpayloads
 */
class ObjectList implements \ArrayAccess, \Iterator
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
     * @var ObjectInterface[]
     */
    protected $entries = [];

    /**
     * The total count of objects across all pages.
     *
     * @var int
     */
    protected $totalResults = 0;

    /**
     * @var ByFields
     */
    protected $byFields;

    /**
     * The position of the array index.
     *
     * @var int
     */
    protected $position = 0;

    /**
     * The factory used to generate the next object list request.
     *
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * The account context used for this list.
     *
     * @var Account
     */
    protected $account;

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

    /**
     * @return ByFields
     */
    public function getByFields(): ByFields
    {
        if (!isset($this->byFields)) {
            throw new \LogicException('This object list does not have byFields set.');
        }

        return $this->byFields;
    }

    /**
     * @param ByFields $byFields
     */
    public function setByFields(ByFields $byFields)
    {
        $this->byFields = $byFields;
    }

    /**
     * Set the objects needed to generate a next request.
     *
     * @param DataObjectFactory $dataObjectFactory The factory used to load the next ObjectList.
     * @param Account           $account           The account context to use for the request.
     */
    public function setDataObjectFactory(DataObjectFactory $dataObjectFactory, Account $account)
    {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->account = $account;
    }

    /**
     * Return if this object list has a next list to load.
     *
     * @return bool True if a next list exists, false otherwise.
     */
    public function hasNext(): bool
    {
        return !empty($this->entries) && ($this->getEntryCount() >= $this->getItemsPerPage());
    }

    /**
     * Return the next object list request, if one exists.
     *
     * @see \Lullabot\Mpx\DataService\ObjectList::setDataObjectFactory
     *
     * @return PromiseInterface|bool A promise to the next ObjectList, or false if no list exists.
     */
    public function nextList()
    {
        if (!$this->hasNext()) {
            return false;
        }

        if (!isset($this->dataObjectFactory) || !isset($this->account)) {
            throw new \LogicException('setDataObjectFactory must be called before calling nextList.');
        }

        if (!isset($this->byFields)) {
            throw new \LogicException('setByFields must be called before calling nextList.');
        }

        $byFields = clone $this->byFields;
        $range = Range::nextRange($this);
        $byFields->setRange($range);

        return $this->dataObjectFactory->selectRequest($byFields, $this->account);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->getEntries()[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->getEntries()[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->entries[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->entries[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->entries[$this->position];
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return isset($this->entries[$this->position]);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        ++$this->position;
    }
}
