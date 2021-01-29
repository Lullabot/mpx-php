<?php

namespace Lullabot\Mpx\DataService;

/**
 * Class for 'by<Field>' filters on requests.
 *
 * @see https://docs.theplatform.com/help/wsf-selecting-objects-by-using-a-byfield-query-parameter
 */
class ByFields implements QueryPartsInterface
{
    /**
     * The array of fields and their value to filter by.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Add a field to this filter, such as 'title'.
     *
     * @param string $field The field to filter on.
     * @param string $value The value to filter by.
     *
     * @return self Fluent return.
     */
    public function addField(string $field, string $value): self
    {
        $this->fields[$field] = $value;

        return $this;
    }

    /**
     * Return all of the fields being filtered.
     */
    public function toQueryParts(): array
    {
        $fields = [];
        foreach ($this->fields as $field => $value) {
            $fields['by'.ucfirst($field)] = $value;
        }

        return $fields;
    }
}
