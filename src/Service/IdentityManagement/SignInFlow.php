<?php

namespace Lullabot\Mpx\Service\IdentityManagement;

use Lullabot\Mpx\Client;
use Lullabot\Mpx\DataService\IdInterface;
use Lullabot\Mpx\Token;

/**
 * Authenticates a user against the mpx identity management service.
 *
 * This is the flow mpx has always used, and remains the default.
 *
 * @see https://docs.theplatform.com/help/wsf-signin-method
 */
class SignInFlow implements AuthenticationFlowInterface
{
    /**
     * The URL to sign in a user.
     */
    final public const SIGN_IN_URL = 'https://identity.auth.theplatform.com/idm/web/Authentication/signIn';

    /**
     * The URL to sign out a given token for a user.
     */
    final public const SIGN_OUT_URL = 'https://identity.auth.theplatform.com/idm/web/Authentication/signOut';

    /**
     * The schema version of the identity management service.
     */
    final public const SCHEMA = '1.0';

    public function acquire(UserInterface $user, Client $client, ?int $duration = null): Token
    {
        $response = $client->request('GET', self::SIGN_IN_URL, $this->signInOptions($user, $duration));

        return Token::fromResponseData(\GuzzleHttp\Utils::jsonDecode($response->getBody(), true));
    }

    public function apply(Token $token, array $options, ?IdInterface $account = null): array
    {
        if (!isset($options['query'])) {
            $options['query'] = [];
        }
        $options['query'] += [
            'token' => $token->getValue(),
        ];

        return $options;
    }

    public function identifier(UserInterface $user): string
    {
        return $user->getMpxUsername();
    }

    public function revoke(Token $token, Client $client): void
    {
        // @todo Handle that the token may be expired.
        // @todo Handle and log that mpx may error on the signout.
        $client->request(
            'GET',
            self::SIGN_OUT_URL,
            [
                'query' => [
                    'schema' => self::SCHEMA,
                    'form' => 'json',
                    '_token' => (string) $token,
                ],
            ]
        );
    }

    /**
     * Return the request options for signing in.
     *
     * @param UserInterface $user     The user to sign in.
     * @param int|null      $duration The duration to sign in for.
     *
     * @return array An array of request options.
     */
    private function signInOptions(UserInterface $user, ?int $duration = null): array
    {
        $options = [];
        $options['auth'] = [
            $user->getMpxUsername(),
            $user->getMpxPassword(),
        ];

        $options['query'] = [
            'schema' => self::SCHEMA,
            'form' => 'json',
        ];

        // @todo move these to POST.
        // https://docs.theplatform.com/help/wsf-signin-method#signInmethod-JSONPOSTexample
        if (!empty($duration)) {
            // API expects this value in milliseconds, not seconds.
            $options['query']['_duration'] = $duration * 1000;
            $options['query']['_idleTimeout'] = $duration * 1000;
        }

        return $options;
    }
}
