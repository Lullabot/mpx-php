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
 *
 * @todo Move to the annotation and convert to CustomKeyInterface.
 */
interface CreateKeyInterface
{
    /**
     * Returns the name of the field containing the canonical identifier, such as 'id'.
     *
     * @return string The ID key.
     */
    public function getIdKey(): string;

    /**
     * Returns the ID of this object.
     *
     * @return \Psr\Http\Message\UriInterface The ID of the object, as a URI.
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
     * @see https://docs.theplatform.com/help/wsf-working-with-custom-fields#Workingwithcustomfields-Uniquecustomvalues
     *
     * @return string[] An array of custom keys.
     */
    public function getCustomKeys(): array;
}
