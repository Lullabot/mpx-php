<?php

namespace Lullabot\Mpx\Service\IdentityManagement;

/**
 * Defines an class for interacting with mpx users.
 *
 * @see http://help.theplatform.com/display/wsf2/Identity+management+service+API+reference
 * @see http://help.theplatform.com/display/wsf2/User+operations
 */
class User implements UserInterface
{
    /**
     * @var string
     */
    private $username;

    /**
     * Construct a new mpx user.
     *
     * @param string $username The mpx user name, including the leading directory such as 'mpx/'.
     * @param string $password The user password.
     */
    public function __construct($username, private $password)
    {
        if (!str_contains($username, '/')) {
            throw new \InvalidArgumentException(sprintf('The mpx user name %s must contain a leading directory such as "mpx/"', $username));
        }

        $this->username = $username;
    }

    /**
     * Get the username of the mpx user.
     *
     * @return string
     */
    public function getMpxUsername()
    {
        return $this->username;
    }

    /**
     * Get the password of the mpx user.
     *
     * @return string
     */
    public function getMpxPassword()
    {
        return $this->password;
    }
}
