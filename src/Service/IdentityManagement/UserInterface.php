<?php

namespace Lullabot\Mpx\Service\IdentityManagement;

/**
 * Interface representing an mpx user.
 */
interface UserInterface
{
    /**
     * Get the username of the MPX user.
     *
     * @return string
     */
    public function getMpxUsername();

    /**
     * Get the password of the MPX user.
     *
     * @return string
     */
    public function getMpxPassword();
}
