<?php

namespace Lullabot\Mpx\DataService;

/**
 * Interface for objects supporting an attached JSON representation.
 *
 * There are times that client applications may need the original JSON response
 * data. For example, when loading an object list, they may want to cache the
 * results individually. In general, use the typed methods directly on each
 * class instead of these methods.
 */
interface JsonInterface
{
    /**
     * Set the original JSON representation of this object.
     */
    public function setJson(string $json);

    /**
     * Return the JSON of this object as an array.
     *
     * @throws \LogicException Thrown if no json representation is available.
     *
     * @return array
     */
    public function getJson();
}
