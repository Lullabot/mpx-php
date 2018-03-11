<?php

namespace Lullabot\Mpx;

use Psr\Http\Message\UriInterface;

/**
 * Defines an interface for create keys.
 *
 * Create keys are used to determine if an object is updated in response to a
 * create request, instead of creating a new object.
 *
 * @see https://docs.theplatform.com/help/wsf-creating-or-updating-data-objects-using-create-keys
 */
interface CreateKeyInterface
{
    /**
     * Returns the name of the field containing the canonical identifier, such as 'id'.
     *
     * @return string
     */
    public function getIdKey(): string;

    /**
     * Returns the ID of this object.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getId(): UriInterface;

    /**
     * Returns an array of all defined compound keys.
     *
     * For example, a Media object would return @code [['ownerId', 'guid']] @endcode.
     *
     * @see https://docs.theplatform.com/help/media-media-object
     *
     * @return array[] An array of compound keys, each compound key as an array.
     */
    public function getCompoundKeys(): array;

    /**
     * Returns an array of all custom keys.
     *
     * Typically, such keys are created by setting isUnique on the field.
     *
     * @return string[]
     */
    public function getCustomKeys(): array;
}
