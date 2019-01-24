<?php

namespace Lullabot\Mpx\DataService;

/**
 * Interface for mpx objects with a GUID field.
 *
 * Note that a "GUID" is not globally unique, but a string set on each asset
 * that is only unique within the owning account.
 */
interface GuidInterface extends OwnerIdInterface
{
    /**
     * Returns an alternate identifier for this object that is unique within the owning account.
     *
     * @return string
     */
    public function getGuid(): ?string;

    /**
     * Set an alternate identifier for this object that is unique within the owning account.
     *
     * @param string $guid
     */
    public function setGuid(?string $guid);
}
