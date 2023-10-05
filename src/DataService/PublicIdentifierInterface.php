<?php

namespace Lullabot\Mpx\DataService;

/**
 * Interface definition for all mpx objects with a public identifier.
 *
 * Note that in practice, it appears that all mpx objects with a public ID also
 * have a GUID. However, having a separate interface makes it easy for calling
 * code to create simple stub objects with just a public identifier, such as
 * when constructing URLs.
 */
interface PublicIdentifierInterface
{
    /**
     * Returns the public identifier for this mpx object.
     */
    public function getPid(): ?string;

    /**
     * Set the public identifier for this mpx object.
     *
     * @param string
     */
    public function setPid(?string $pid);
}
