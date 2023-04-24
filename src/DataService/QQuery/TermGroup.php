<?php

namespace Lullabot\Mpx\DataService\QQuery;

use Lullabot\Mpx\DataService\QueryPartsInterface;

/**
 * Represents terms grouped with AND, OR, and parenthesis.
 *
 * @see https://docs.theplatform.com/help/wsf-selecting-objects-by-using-the-q-query-parameter
 */
class TermGroup implements QueryPartsInterface, TermInterface, \Stringable
{
    private array $terms = [];

    private ?bool $wrap = null;

    public function __construct(TermInterface $term)
    {
        $this->terms[] = [$term];
    }

    public function and(TermInterface $term): self
    {
        $this->terms[] = [
            ' AND',
            $term,
        ];

        return $this;
    }

    public function or(TermInterface $term): self
    {
        $this->terms[] = [
            ' OR',
            $term,
        ];

        return $this;
    }

    public function wrapParenthesis($wrap = true): self
    {
        $this->wrap = $wrap;

        return $this;
    }

    public function __toString(): string
    {
        $query = '';
        foreach ($this->terms as $term) {
            $query .= implode(' ', $term);
        }

        if ($this->wrap) {
            $query = '('.$query.')';
        }

        return $query;
    }

    public function toQueryParts(): array
    {
        return [
            'q' => (string) $this,
        ];
    }
}
