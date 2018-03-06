<?php

namespace Mpx;

/**
 * Defines an class for interacting with MPX users.
 *
 * @see http://help.theplatform.com/display/wsf2/Identity+management+service+API+reference
 * @see http://help.theplatform.com/display/wsf2/User+operations
 */
class User
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * Construct a new MPX user.

    *
     * @param string $username
     * @param string $password

     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Get the username of the MPX user.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get the password of the MPX user.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}
