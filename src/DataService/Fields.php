<?php

namespace Lullabot\Mpx\DataService;

/**
 * Class for 'fields' filters on requests.
 *
 * @see https://docs.theplatform.com/help/wsf-controlling-the-contents-of-the-response-payload
 */
class Fields implements QueryPartsInterface
{
    /**
     * The array of fields to be returned in the result.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Add a field to this filter, such as 'title'.
     *
     * @param string $field The field to filter on.
     *
     * @return self Fluent return.
     */
    public function addField(string $field): self
    {
        $this->fields[] = $field;

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

        $fields['fields'] = implode(',', $this->fields);

        return $fields;
    }
}
