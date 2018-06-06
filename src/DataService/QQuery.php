<?php

namespace Lullabot\Mpx\DataService;

/**
 * @see https://docs.theplatform.com/help/wsf-selecting-objects-by-using-the-q-query-parameter
 */
class QQuery
{

    /**
     * @var array
     */
    private $terms = [];

    public function __construct(Term $term)
    {
        $this->terms[] = [$term];
    }

    public function and(Term $term)
    {
        $this->terms[] = [
            ' AND',
            $term,
        ];
    }

    public function or(Term $term)
    {
        $this->terms[] = [
            ' OR',
            $term,
        ];
    }

    public function __toString()
    {
        $query = "";
        foreach ($this->terms as $term) {
            $query .= implode(' ', $term);
        }
        return $query;
    }

    public function toQueryParts(): array
    {
        return [
            'q' => [
                (string) $this,
            ],
        ];
    }

}
