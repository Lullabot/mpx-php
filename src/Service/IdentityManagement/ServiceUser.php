<?php

namespace Lullabot\Mpx\Service\IdentityManagement;

/**
 * Defines a class for interacting with CTS/CVP service users.
 *
 * Unlike User, no leading directory is required in the identifier: an OAuth
 * client ID is an opaque string such as '0oazmvvnwr8dxUG9J417'.
 *
 * @see ServiceTokenFlow
 */
class ServiceUser implements ServiceCredentialsInterface
{
    /**
     * Construct a new mpx service user.
     *
     * @param string $clientId     The OAuth client ID.
     * @param string $clientSecret The OAuth client secret.
     */
    public function __construct(private readonly string $clientId, private readonly string $clientSecret)
    {
        if ('' === $clientId) {
            throw new \InvalidArgumentException('The mpx service client ID must not be empty.');
        }
    }

    public function getMpxClientId(): string
    {
        return $this->clientId;
    }

    public function getMpxClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * Get the username of the mpx user.
     *
     * The client ID doubles as the username: the service token endpoint takes
     * the client ID and secret as an HTTP Basic pair.
     *
     * @return string
     */
    public function getMpxUsername()
    {
        return $this->clientId;
    }

    /**
     * Get the password of the mpx user.
     *
     * @see self::getMpxUsername()
     *
     * @return string
     */
    public function getMpxPassword()
    {
        return $this->clientSecret;
    }
}
