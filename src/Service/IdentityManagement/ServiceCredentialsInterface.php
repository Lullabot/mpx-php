<?php

namespace Lullabot\Mpx\Service\IdentityManagement;

/**
 * Interface representing CTS/CVP service credentials.
 *
 * Service users authenticate with an OAuth client ID and secret rather than an
 * mpx username and password. This interface extends UserInterface so service
 * credentials can be passed anywhere a user is expected, but callers that need
 * to be explicit about what they are handling should use the accessors
 * declared here.
 */
interface ServiceCredentialsInterface extends UserInterface
{
    /**
     * Get the OAuth client ID of the service user.
     */
    public function getMpxClientId(): string;

    /**
     * Get the OAuth client secret of the service user.
     */
    public function getMpxClientSecret(): string;
}
