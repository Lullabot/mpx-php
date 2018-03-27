<?php

namespace Lullabot\Mpx;

use Lullabot\Mpx\Exception\TokenNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\StoreInterface;

/**
 * Defines an class for interacting with MPX users.
 *
 * @see http://help.theplatform.com/display/wsf2/Identity+management+service+API+reference
 * @see http://help.theplatform.com/display/wsf2/User+operations
 */
class User
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
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;
    /**
     * @var Client
     */
    protected $client;
    /**
     * @var StoreInterface
     */
    protected $store;
    /**
     * @var TokenCachePool
     */
    protected $tokenCachePool;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Construct a new MPX user.

     *
     * @param \Lullabot\Mpx\Client         $client         The client used to access MPX.
     * @param StoreInterface               $store          The lock backend to store locks in.
     * @param \Lullabot\Mpx\TokenCachePool $tokenCachePool The cache of authentication tokens.
     * @param LoggerInterface              $logger         The logger used when logging automatic authentication renewals.
     * @param string                       $username
     * @param string                       $password
     */
    public function __construct(Client $client, StoreInterface $store, TokenCachePool $tokenCachePool, LoggerInterface $logger, $username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->client = $client;
        $this->store = $store;
        $this->tokenCachePool = $tokenCachePool;
        $this->logger = $logger;
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

    /**
     * Get a current authentication token for the account.
     *
     * This method will automatically generate a new token if one does not exist.
     *
     * @todo Do we want to make this async?
     *
     * @param int  $duration (optional) The number of seconds for which the token should be valid.
     * @param bool $reset    Force fetching a new token, even if one exists.
     *
     * @return \Lullabot\Mpx\Token A valid MPX authentication token.
     */
    public function acquireToken(int $duration = null, bool $reset = false): Token
    {
        if ($reset) {
            $this->tokenCachePool->deleteToken($this);
        }

        // We assume that the cache is backed by shared storage across multiple
        // requests. In that case, it's possible for another thread to set a
        // token between the above delete and the next try block.
        try {
            $token = $this->tokenCachePool->getToken($this);
        } catch (TokenNotFoundException $e) {
            $token = $this->signInWithLock($duration);
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
    protected function signIn($duration = null): Token
    {
        $options = [];
        $options['auth'] = [
            $this->getUsername(),
            $this->getPassword(),
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

        $token = $this->tokenFromResponse($data);
        $this->logger->info(
            'Retrieved a new MPX token {token} for user {username} that expires on {date}.',
            [
                'token' => $token->getValue(),
                'username' => $this->getUsername(),
                'date' => date(DATE_ISO8601, $token->getExpiration()),
            ]
        );

        return $token;
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
                    '_token' => (string) $this->tokenCachePool->getToken($this),
                ],
            ]
        );

        $this->tokenCachePool->deleteToken($this);
    }

    /**
     * Sign in to MPX, with a lock to prevent sign-in stampedes.
     *
     * @param int $duration (optional) The number of seconds that the sign-in token should be valid for.
     *
     * @return Token
     */
    protected function signInWithLock(int $duration = null): Token
    {
        $factory = new Factory($this->store);
        $factory->setLogger($this->logger);
        $lock = $factory->createLock($this->getUsername(), 10);

        // Blocking means this will throw an exception on failure.
        $lock->acquire(true);

        try {
            // It's possible another thread has signed in for us, so check for a token first.
            $token = $this->tokenCachePool->getToken($this);
        } catch (TokenNotFoundException $e) {
            // We have the lock, and there's no token, so sign in.
            $token = $this->signIn($duration);
        }

        return $token;
    }

    /**
     * Instantiate and cache a token.
     *
     * @param array $data The MPX signIn() response data.
     *
     * @return \Lullabot\Mpx\Token The new token.
     */
    private function tokenFromResponse(array $data): Token
    {
        $token = Token::fromResponseData($data);
        // Save the token to the cache and return it.
        $this->tokenCachePool->setToken($this, $token);

        return $token;
    }
}
