<?php

namespace Lullabot\Mpx\Service\IdentityManagement;

use Lullabot\Mpx\Client;
use Lullabot\Mpx\Token;
use Lullabot\Mpx\TokenCachePool;
use Lullabot\Mpx\User;
use Psr\Log\LoggerInterface;

class UserSession
{
    /**
     * The URL to sign in a user.
     */
    const SIGN_IN_URL = 'https://identity.auth.theplatform.com/idm/web/Authentication/signIn';

    /**
     * The URL to sign out a given token for a user.
     */
    const SIGN_OUT_URL = 'https://identity.auth.theplatform.com/idm/web/Authentication/signOut';

    /**
     * The user to establish a session for.
     *
     * @var \Lullabot\Mpx\User
     */
    protected $user;

    /**
     * The cache of authentication tokens.
     *
     * @var \Lullabot\Mpx\TokenCachePool
     */
    protected $tokenCachePool;

    /**
     * The client used to access MPX.
     *
     * @var \Lullabot\Mpx\Client
     */
    protected $client;

    /**
     * The logger used to log automatic token renewals.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Construct a new user session.
     *
     * Note that the session is not actually established until acquireToken is
     * called.
     *
     * @param \Lullabot\Mpx\Client         $client         The client used to access MPX.
     * @param \Lullabot\Mpx\User           $user           The user associated with this session.
     * @param \Lullabot\Mpx\TokenCachePool $tokenCachePool The cache of authentication tokens.
     * @param \Psr\Log\LoggerInterface     $logger         The logger used when logging automatic authentication renewals.
     *
     * @see \Psr\Log\NullLogger To disable logging within this session.
     */
    public function __construct(Client $client, User $user, TokenCachePool $tokenCachePool, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->user = $user;
        $this->tokenCachePool = $tokenCachePool;
        $this->logger = $logger;
    }

    /**
     * Get a current authentication token for the account.
     *
     * This method will automatically generate a new token if one does not exist.
     *
     * @param int $duration (optional) The number of seconds for which the token should be valid.
     *
     * @return \Lullabot\Mpx\Token A valid MPX authentication token.
     */
    public function acquireToken($duration = null): Token {
        try {
            $token = $this->tokenCachePool->getToken($this->user);
        } catch (\RuntimeException $e) {
            // @todo This catch should be a more specific exception.
            $token = $this->signIn($duration);
            $this->logger->info(
                'Refreshed token with a new mpx token {token} for user {username} that expires on {date}.',
                [
                    'token' => $token->getValue(),
                    'username' => $this->user->getUsername(),
                    'date' => date(DATE_ISO8601, $token->getExpiration()),
                ]
            );
        }

        return $token;
    }

    /**
     * Sign in the user and return the current token.
     *
     * @param int $duration (optional) The number of seconds for which the token should be valid.
     *
     * @return \Lullabot\Mpx\Token
     */
    protected function signIn($duration = null): Token {
        $options = [];
        $options['auth'] = [
            $this->user->getUsername(),
            $this->user->getPassword(),
        ];

        // @todo Make this a class constant.
        $options['query'] = [
            'schema' => '1.0',
            'form' => 'json',
        ];

        // @todo move these to POST.
        // https://docs.theplatform.com/help/wsf-signin-method#signInmethod-JSONPOSTexample
        if (!empty($duration)) {
            // API expects this value in milliseconds, not seconds.
            $options['query']['_duration'] = $duration * 1000;
            $options['query']['_idleTimeout'] = $duration * 1000;
        }

        $response = $this->client->request(
            'GET',
            self::SIGN_IN_URL,
            $options
        );

        $data = \GuzzleHttp\json_decode($response->getBody(), true);

        return $this->tokenFromResponse($data);
    }

    /**
     * Sign out the user.
     */
    public function signOut()
    {
        // @todo Handle that the token may be expired.
        // @todo Handle and log that MPX may error on the signout.
        $this->client->request(
            'GET',
            self::SIGN_OUT_URL,
            [
                'query' => [
                    'schema' => '1.0',
                    'form' => 'json',
                    '_token' => (string) $this->tokenCachePool->getToken($this->user),
                ],
            ]
        );

        $this->tokenCachePool->deleteToken($this->user);
    }

    /**
     * Instantiate and cache a token.
     *
     * @param array $data The MPX signIn() response data.
     *
     * @return \Lullabot\Mpx\Token The new token.
     */
    private function tokenFromResponse(array $data): Token {
        $token = Token::fromResponse($data);
        // Save the token to the cache and return it.
        $this->tokenCachePool->setToken($this->user, $token);

        return $token;
    }
}
