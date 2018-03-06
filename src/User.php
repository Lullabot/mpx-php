<?php

namespace Mpx;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

/**
 * Defines an class for interacting with MPX users.
 *
 * @see http://help.theplatform.com/display/wsf2/Identity+management+service+API+reference
 * @see http://help.theplatform.com/display/wsf2/User+operations
 */
class User
{
    use HasCachePoolTrait;
    use HasClientTrait;
    use HasLoggerTrait;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $tokenCacheKey;

    /**
     * @var \Mpx\Token|null
     */
    private $token;

    /**
     * @param string                            $username
     * @param string                            $password
     * @param \Mpx\Client                       $client
     * @param \Psr\Cache\CacheItemPoolInterface $cachePool
     * @param \Psr\Log\LoggerInterface          $logger
     */
    public function __construct($username, $password, Client $client = null, CacheItemPoolInterface $cachePool = null, LoggerInterface $logger = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->client = $client;
        $this->cachePool = $cachePool;
        $this->logger = $logger;

        // Fetch the current token from the cache.
        $this->tokenCacheKey = 'token|'.md5($this->getUsername());
        $this->token = $this->getCachePool()->getItem($this->tokenCacheKey)->get();
    }

    /**
     * Get the username of the mpx user.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get the password of the mpx user.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Return the current token for the account.
     *
     * @return \Mpx\Token|null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the current token for the account.
     *
     * @param \Mpx\Token $token
     *                          The token to associate with the account.
     *
     * @return static
     */
    public function setToken(Token $token)
    {
        $this->token = $token;

        // Save the token to the cache pool.
        $item = $this->getCachePool()->getItem($this->tokenCacheKey);
        $item->set($token);
        $item->expiresAfter($token->getLifetime());
        $this->getCachePool()->save($item);

        return $this;
    }

    /**
     * Get a current authentication token for the account.
     *
     * @param int  $duration
     *                       The number of seconds for which the token should be valid.
     * @param bool $force
     *                       Set to TRUE if a fresh authentication token should always be fetched.
     *
     * @return string
     *                A valid MPX authentication token.
     */
    public function acquireToken($duration = null, $force = false)
    {
        $token = $this->getToken();

        if ($force || !$token || !$token->isValid($duration)) {
            if ($token) {
                $this->signOut();
            }
            $token = $this->signIn();
        }

        return $token;
    }

    /**
     * Invalidate the current authentication token for the account.
     */
    public function invalidateToken()
    {
        $this->token = null;
        $this->getCachePool()->deleteItem($this->tokenCacheKey);
    }

    /**
     * Sign in the user and return the current token.
     */
    public function signIn($duration = null)
    {
        $options = [];
        $options['auth'] = [
            $this->getUsername(),
            $this->getPassword(),
        ];
        $options['query'] = [
            'schema' => '1.0',
            'form' => 'json',
        ];

        if (!empty($duration)) {
            // API expects this value in milliseconds, not seconds.
            $options['query']['_duration'] = $duration * 1000;
            $options['query']['_idleTimeout'] = $duration * 1000;
        }

        $data = $this->getClient()->request(
            'GET',
            'https://identity.auth.theplatform.com/idm/web/Authentication/signIn',
            $options
        );

        $lifetime = (int) floor(min($data['signInResponse']['duration'], $data['signInResponse']['idleTimeout']) / 1000);
        $token = new Token($data['signInResponse']['token'], $lifetime);

        $this->getLogger()->info(
            'Fetched new mpx token {token} for user {username} that expires on {date}.',
            [
                'token' => $token->getValue(),
                'username' => $this->getUsername(),
                'date' => date(DATE_ISO8601, $token->getExpiration()),
            ]
        );

        // Save the token to the cache and return it.
        $this->setToken($token);

        return $token;
    }

    /**
     * Sign out the user.
     */
    public function signOut()
    {
        if ($token = $this->getToken()) {
            $this->getClient()->request(
                'GET',
                'https://identity.auth.theplatform.com/idm/web/Authentication/signOut',
                [
                    'query' => [
                        'schema' => '1.0',
                        'form' => 'json',
                        '_token' => (string) $token,
                    ],
                ]
            );

            $this->getLogger()->info(
                'Expired mpx token {token} for {username}.',
                [
                    'token' => $token->getValue(),
                    'username' => $this->getUsername(),
                ]
            );

            $this->invalidateToken();
        }
    }

    protected function getSelfId()
    {
        $item = $this->getCachePool()->getItem('user|'.md5($this->getUsername()));
        $data = $item->get();

        if (!$item->isHit()) {
            $data = $this->getClient()->authenticatedRequest(
                $this,
                'GET',
                'https://identity.auth.theplatform.com/idm/web/Self/getSelfId',
                [
                    'query' => [
                        'schema' => '1.0',
                        'form' => 'json',
                    ],
                ]
            );
            $data = $data['getSelfIdResponse'];
            $item->set($data);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        $data = $this->getSelfId();

        return basename($data['userId']);
    }
}
