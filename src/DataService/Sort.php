<?php

namespace Lullabot\Mpx\DataService;

/**
 * Represents a sort on a request.
 *
 * @see https://docs.theplatform.com/help/wsf-controlling-the-contents-of-the-response-payload#tp-toc19
 */
class Sort
{
    /**
     * The array of fields and their query value to sort by.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Add a sort to this query.
     *
     * @param string $field      The field to sort on, such as 'id' or 'title'.
     * @param bool   $descending (optional) True if results should be sorted descending, false otherwise.
     *
     * @return self
     */
    public function addSort(string $field, $descending = false): self
    {
        $this->fields[$field] = $field.($descending ? '|desc' : '');

        return $this;
    }

    /**
     * Return this sort as an array suitable for a Guzzle query.
     *
     * @return array An array with a 'sort' key, or an empty array if no sorts are set.
     */
    public function toQueryParts()
    {
        if (empty($this->fields)) {
            return [];
        }

        return [
            'sort' => implode(',', $this->fields),
        ];
    }
}
