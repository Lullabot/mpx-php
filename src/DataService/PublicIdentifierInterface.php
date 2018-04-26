<?php

namespace Lullabot\Mpx\DataService;

/**
 * Interface definition for all mpx objects with a public identifier.
 */
interface PublicIdentifierInterface
{
    /**
     * Returns the public identifier for this mpx object.
     *
     * @return string
     */
    public function getPid(): string;

    /**
     * Set the public identifier for this mpx object.
     *
     * @param string
     */
    public function setPid($pid);
}
