<?php

namespace Lullabot\Mpx\DataService;

/**
 * A collection of fields to filter a request by.
 *
 * Results from a ByField query are paged, and MPX enforces paging if none is
 * specified. Since sorting may be inconsistent across pages, this class will
 * automatically sort by 'id' if no sort is specified.
 *
 * By default, pages are set to 100 items per page. This matches well with
 * memory consumption (where PHP leaks memory at the default 500 items mpx
 * returns) and CPU use.
 *
 * @see https://docs.theplatform.com/help/wsf-selecting-objects-by-using-a-byfield-query-parameter
 */
class ByFields
{
    /**
     * The array of fields and their value to filter by.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * The sort to apply to this filter.
     *
     * @var Sort
     */
    protected $sort;

    /**
     * The range of objects to filter by.
     *
     * @var Range
     */
    protected $range;

    /**
     * ByFields constructor.
     */
    public function __construct()
    {
        $this->sort = new Sort();
        $this->sort->addSort('id');
        $this->range = new Range();
        $this->range->setStartIndex(1);
        $this->range->setEndIndex(101);
    }

    /**
     * Add a field to this filter, such as 'title'.
     *
     * @param string $field The field to filter on.
     * @param string $value The value to filter by.
     */
    public function addField(string $field, string $value)
    {
        $this->fields[$field] = $value;
    }

    /**
     * Set a range to apply to this request. MPX will default to a 1-500 range.
     *
     * @param Range $range The range object to add.
     */
    public function setRange(Range $range)
    {
        $this->range = $range;
    }

    /**
     * Set a sort to apply to this request.
     *
     * @param Sort $sort The sort object to add.
     */
    public function setSort(Sort $sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return Sort
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @return Range
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * Return all of the fields being filtered.
     *
     * @return array
     */
    protected function getFields()
    {
        $fields = [];
        foreach ($this->fields as $field => $value) {
            $fields['by'.ucfirst($field)] = $value;
        }

        return $fields;
    }

    /**
     * Return an array suitable for use within a Guzzle 'query' parameter.
     *
     * @todo Consider making this a chain of callables?
     *
     * @return array The array of query arguments.
     */
    public function toQueryParts()
    {
        return $this->getFields() + $this->getSort()->toQueryParts() + $this->getRange()->toQueryParts();
    }
}
