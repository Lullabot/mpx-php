<?php

namespace Lullabot\Mpx\DataService;

use GuzzleHttp\Promise\PromiseInterface;

/**
 * An iterator over a list of MPX objects.
 *
 * This iterator wraps pages of MPX results, allowing them to be transparently
 * accessed in foreach loops. This class does not allow rewinding, as that would
 * require storing all results in memory which could be very large for typical
 * requests.
 *
 * While each ObjectList has it's own index, this iterator allows for accessing
 * the combined set of lists as if they were one single response.
 *
 * @todo If rewinding is useful, store sets of indexed byField queries, noting
 *       data would be recreated.
 * @todo If skipping ahead is useful (you'd have to somehow know the index) use
 *       totalResults on the current list to save having to query all the
 *       results in between.
 */
class ObjectListIterator extends \NoRewindIterator
{
    /**
     * The global position in the object list.
     *
     * @var int
     */
    protected $position = 0;

    /**
     * The page of results this object list is on.
     *
     * @var int
     */
    protected $page = 0;

    /**
     * The current object list page.
     *
     * @var ObjectList
     */
    protected $list;

    /**
     * The initial promise to return an object list.
     *
     * @var PromiseInterface
     */
    protected $promise;

    /**
     * The relative position within the current object list page.
     *
     * @var int
     */
    protected $relative;

    /**
     * ObjectListIterator constructor.
     *
     * @param PromiseInterface $promise A promise to return an ObjectList.
     */
    public function __construct(PromiseInterface $promise)
    {
        $this->promise = $promise;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->list[$this->relative];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        ++$this->position;
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
        // Initial setup if this is the first page.
        if (empty($this->list)) {
            $this->list = $this->promise->wait();
        }
        $requested_page = floor($this->position / $this->list->getItemsPerPage());

        while ($requested_page > $this->page) {
            // There is no next page to retrieve, but we just asked to go beyond the last page.
            if (!$next = $this->list->nextList()) {
                return false;
            }

            $this->list = $next->wait();
            ++$this->page;
        }

        // Now, figure out the relative index.
        $this->relative = $this->position % $this->list->getItemsPerPage();
        if (isset($this->list[$this->relative])) {
            return true;
        }

        // The last page is not completely full, but the relative position does
        // not exist.
        return false;
    }

    /**
     * Returns the number total results.
     *
     * @return int
     */
    public function getTotalResults(): int
    {
        return $this->list->getTotalResults();
    }

}
