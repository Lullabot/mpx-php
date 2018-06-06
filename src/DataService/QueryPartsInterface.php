<?php

namespace Lullabot\Mpx\DataService;

interface QueryPartsInterface
{

    /**
     * Return an array suitable for use within a Guzzle 'query' parameter.
     *
     * @return array The array of query arguments.
     */
    public function toQueryParts(): array;
}
