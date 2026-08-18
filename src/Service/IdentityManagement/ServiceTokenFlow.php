<?php

namespace Lullabot\Mpx\Service\IdentityManagement;

use Lullabot\Mpx\Client;
use Lullabot\Mpx\DataService\IdInterface;
use Lullabot\Mpx\Token;

/**
 * Authenticates a CTS/CVP service user with the client credentials flow.
 *
 * The service token endpoint exchanges an OAuth client ID and secret for a JWT,
 * and returns it in the same signInResponse envelope the identity management
 * service uses. The token lifetime is controlled server-side, so the requested
 * duration is ignored.
 */
class ServiceTokenFlow implements AuthenticationFlowInterface
{
    /**
     * The URL to exchange service credentials for a token.
     */
    final public const SERVICE_TOKEN_URL = 'https://fedauth.theplatform.com/v1/service/token';

    /**
     * The prefix applied to cache keys and lock names.
     *
     * Service tokens and sign-in tokens are not interchangeable, so they must
     * never share a cache entry even when the credentials look the same.
     */
    final public const IDENTIFIER_PREFIX = 'service-token:';

    public function acquire(UserInterface $user, Client $client, ?int $duration = null): Token
    {
        // The endpoint takes no query parameters or request body, and ignores
        // any requested lifetime: it is set in Okta.
        $response = $client->request(
            'GET',
            self::SERVICE_TOKEN_URL,
            [
                'auth' => [
                    $this->clientId($user),
                    $this->clientSecret($user),
                ],
            ]
        );

        return Token::fromResponseData(\GuzzleHttp\Utils::jsonDecode($response->getBody(), true));
    }

    public function apply(Token $token, array $options, ?IdInterface $account = null): array
    {
        // The token is sent with HTTP Basic auth, where the username is the
        // account context and the password is the token. An empty username is
        // valid, and means the request is not scoped to a single account.
        $context = $account ? (string) $account->getMpxId() : '';
        $options['headers']['Authorization'] = 'Basic '.base64_encode($context.':'.$token->getValue());

        return $options;
    }

    public function identifier(UserInterface $user): string
    {
        return self::IDENTIFIER_PREFIX.$this->clientId($user);
    }

    /**
     * Revoke a token.
     *
     * Service tokens cannot be revoked; they expire server-side.
     */
    public function revoke(Token $token, Client $client): void
    {
    }

    /**
     * Return the OAuth client ID for a user.
     *
     * Users that do not implement ServiceCredentialsInterface fall back to the
     * username, which the service token endpoint accepts as the client ID.
     */
    private function clientId(UserInterface $user): string
    {
        return $user instanceof ServiceCredentialsInterface ? $user->getMpxClientId() : $user->getMpxUsername();
    }

    /**
     * Return the OAuth client secret for a user.
     *
     * @see self::clientId()
     */
    private function clientSecret(UserInterface $user): string
    {
        return $user instanceof ServiceCredentialsInterface ? $user->getMpxClientSecret() : $user->getMpxPassword();
    }
}
