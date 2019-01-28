<?php

namespace Lullabot\Mpx\Service\IdentityManagement;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Lullabot\Mpx\Client;
use Lullabot\Mpx\Exception\TokenNotFoundException;
use Lullabot\Mpx\Token;
use Lullabot\Mpx\TokenCachePool;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\StoreInterface;

/**
 * Defines a class for authenticating a user with mpx.
 *
 * @see http://help.theplatform.com/display/wsf2/Identity+management+service+API+reference
 * @see http://help.theplatform.com/display/wsf2/User+operations
 */
class UserSession
{
    use LoggerAwareTrait;

    /**
     * The URL to sign in a user.
     */
    const SIGN_IN_URL = 'https://identity.auth.theplatform.com/idm/web/Authentication/signIn';

    /**
     * The URL to sign out a given token for a user.
     */
    const SIGN_OUT_URL = 'https://identity.auth.theplatform.com/idm/web/Authentication/signOut';

    /**
     * @var Client
     */
    protected $client;

    /**
     * The backend lock store used to store a lock when signing in to mpx.
     *
     * @var StoreInterface
     */
    protected $store;

    /**
     * The cache of authentication tokens.
     *
     * @var TokenCachePool
     */
    protected $tokenCachePool;

    /**
     * The user to authenticate as.
     *
     * @var UserInterface
     */
    protected $user;

    /**
     * Construct a new mpx user.
     *
     * @see \Psr\Log\NullLogger To disable logging of token requests.
     *
     * @param UserInterface  $user           The user to authenticate as.
     * @param Client         $client         The client used to access mpx.
     * @param StoreInterface $store          (optional) The lock backend to store locks in.
     * @param TokenCachePool $tokenCachePool (optional) The cache of authentication tokens.
     */
    public function __construct(UserInterface $user, Client $client, StoreInterface $store = null, TokenCachePool $tokenCachePool = null)
    {
        $this->user = $user;
        $this->client = $client;
        $this->store = $store;
        if (!$tokenCachePool) {
            $tokenCachePool = new ArrayCachePool();
        }
        $this->tokenCachePool = $tokenCachePool;
        $this->logger = new NullLogger();
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
     * @return Token A valid mpx authentication token.
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
     * @return Token
     */
    protected function signIn($duration = null): Token
    {
        $options = $this->signInOptions($duration);

        $response = $this->client->request(
            'GET',
            self::SIGN_IN_URL,
            $options
        );

        $data = \GuzzleHttp\json_decode($response->getBody(), true);

        $token = $this->tokenFromResponse($data);
        $this->logger->info(
            'Retrieved a new mpx token {token} for user {username} that expires on {date}.',
            [
                'token' => $token->getValue(),
                'username' => $this->user->getMpxUsername(),
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
        // @todo Handle and log that mpx may error on the signout.
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
     * Sign in to mpx, with a lock to prevent sign-in stampedes.
     *
     * @param int $duration (optional) The number of seconds that the sign-in token should be valid for.
     *
     * @return Token
     */
    protected function signInWithLock(int $duration = null): Token
    {
        if ($this->store) {
            $factory = new Factory($this->store);
            $factory->setLogger($this->logger);
            $lock = $factory->createLock($this->user->getMpxUsername(), 10);

            // Blocking means this will throw an exception on failure.
            $lock->acquire(true);
        }

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
     * @param array $data The mpx signIn() response data.
     *
     * @return Token The new token.
     */
    private function tokenFromResponse(array $data): Token
    {
        $token = Token::fromResponseData($data);
        // Save the token to the cache and return it.
        $this->tokenCachePool->setToken($this, $token);

        return $token;
    }

    /**
     * Return the query parameters for signing in.
     *
     * @param int $duration The duration to sign in for.
     *
     * @return array An array of query parameters.
     */
    private function signInOptions($duration = null): array
    {
        $options = [];
        $options['auth'] = [
            $this->user->getMpxUsername(),
            $this->user->getMpxPassword(),
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

        return $options;
    }

    /**
     * Return the user associated with this session.
     *
     * @return User
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
