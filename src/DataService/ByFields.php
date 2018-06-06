<?php

namespace Lullabot\Mpx\DataService;

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
     */
    public function addField(string $field, string $value)
    {
        $this->fields[$field] = $value;

        return $this;
    }

    /**
     * Return all of the fields being filtered.
     *
     * @return array
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
