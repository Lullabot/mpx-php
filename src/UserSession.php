<?php

namespace Lullabot\Mpx;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Promise;
use Lullabot\Mpx\Exception\ClientException;
use Lullabot\Mpx\Exception\TokenNotFoundException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class UserSession implements ClientInterface
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
     * @todo There is a potential cache stampede on signing in.
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
            $this->tokenCachePool->deleteToken($this->user);
        }

        // We assume that the cache is backed by shared storage across multiple
        // requests. In that case, it's possible for another thread to set a
        // token between the above delete and the next try block.
        try {
            $token = $this->tokenCachePool->getToken($this->user);
        } catch (TokenNotFoundException $e) {
            $token = $this->signIn($duration);
            $this->logger->info(
                'Retrieved a new MPX token {token} for user {username} that expires on {date}.',
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
    protected function signIn($duration = null): Token
    {
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
     * {@inheritdoc}
     */
    public function send(RequestInterface $request, array $options = [])
    {
        return $this->sendWithRetry($request, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function sendAsync(RequestInterface $request, array $options = [])
    {
        return $this->sendAsyncWithRetry($request, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function request($method, $uri, array $options = [])
    {
        return $this->requestWithRetry($method, $uri, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function requestAsync($method, $uri, array $options = [])
    {
        return $this->requestAsyncWithRetry($method, $uri, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig($option = null)
    {
        return $this->client->getConfig($option);
    }

    /**
     * Merge authentication headers into request options.
     *
     * @param array $options The array of request options.
     * @param bool  $reset   Acquire a new token even if one is cached.
     *
     * @return array The updated request options.
     */
    private function mergeAuth(array $options, bool $reset = false): array
    {
        if (!isset($options['query'])) {
            $options['query'] = [];
        }
        $options['query'] += [
            'token' => $this->acquireToken(null, $reset)->getValue(),
        ];

        return $options;
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
        $token = Token::fromResponse($data);
        // Save the token to the cache and return it.
        $this->tokenCachePool->setToken($this->user, $token);

        return $token;
    }

    /**
     * Send a request, retrying once if the authentication token is invalid.
     *
     * This method creates three promises. The outer promise is what is
     * returned, and itself contains two promises. The first promise is the
     * first attempt to make an authenticated request. If it fails, a second
     * promise is created that explicitly rejects the outer promise, as we only
     * want to retry once.
     *
     * @param \Psr\Http\Message\RequestInterface $request The request to send.
     * @param array                              $options Request options to apply.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface|\Psr\Http\Message\RequestInterface
     */
    private function sendAsyncWithRetry(RequestInterface $request, array $options)
    {
        /** @var \GuzzleHttp\Promise\Promise $promise */
        $promise = new Promise(function () use (&$promise, $request, $options) {
            $merged = $this->mergeAuth($options);
            /** @var \GuzzleHttp\Promise\PromiseInterface $first */
            $first = $this->client->sendAsync($request, $merged);
            $first->then(function (ResponseInterface $response) use ($promise) {
                $promise->resolve($response);
            }, function (RequestException $e) use ($promise, $request, $options) {
                // Only retry if it's a token auth error.
                if (!($e instanceof ClientException) || 401 != $e->getCode()) {
                    $promise->reject($e);
                }

                $merged = $this->mergeAuth($options, true);
                /** @var \GuzzleHttp\Promise\PromiseInterface $second */
                $second = $this->client->sendAsync($request, $merged);
                $second->then(function (ResponseInterface $response) use ($promise) {
                    $promise->resolve($response);
                }, function (RequestException $e) use ($promise) {
                    $promise->reject($e);
                });
            });
        });

        return $promise;
    }

    /**
     * Send a request, retrying once if the authentication token is invalid.
     *
     * @param \Psr\Http\Message\RequestInterface $request The request to send.
     * @param array                              $options An array of request options.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function sendWithRetry(RequestInterface $request, array $options)
    {
        $merged = $this->mergeAuth($options);

        try {
            return $this->client->send($request, $merged);
        } catch (ClientException $e) {
            // Only retry if MPX has returned that the existing token is no
            // longer valid.
            if (401 != $e->getCode()) {
                throw $e;
            }

            $merged = $this->mergeAuth($options, true);

            return $this->client->send($request, $merged);
        }
    }

    /**
     * Create and send a request, retrying once if the authentication token is invalid.
     *
     * This method creates three promises. The outer promise is what is
     * returned, and itself contains two promises. The first promise is the
     * first attempt to make an authenticated request. If it fails, a second
     * promise is created that explicitly rejects the outer promise, as we only
     * want to retry once.
     *
     * @param string                                $method  HTTP method
     * @param string|\Psr\Http\Message\UriInterface $uri     URI object or string.
     * @param array                                 $options Request options to apply.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface|\Psr\Http\Message\RequestInterface
     */
    private function requestAsyncWithRetry(string $method, $uri, array $options)
    {
        /** @var \GuzzleHttp\Promise\Promise $promise */
        $promise = new Promise(function () use (&$promise, $method, $uri, $options) {
            $merged = $this->mergeAuth($options);
            /** @var \GuzzleHttp\Promise\PromiseInterface $first */
            $first = $this->client->requestAsync($method, $uri, $merged);
            $first->then(function (ResponseInterface $response) use ($promise) {
                $promise->resolve($response);
            }, function (RequestException $e) use ($promise, $method, $uri, $options) {
                // Only retry if it's a token auth error.
                if (!($e instanceof ClientException) || 401 != $e->getCode()) {
                    $promise->reject($e);
                }

                $merged = $this->mergeAuth($options, true);
                /** @var \GuzzleHttp\Promise\PromiseInterface $second */
                $second = $this->client->requestAsync($method, $uri, $merged);
                $second->then(function (ResponseInterface $response) use ($promise) {
                    $promise->resolve($response);
                }, function (RequestException $e) use ($promise) {
                    $promise->reject($e);
                });
            });
        });

        return $promise;
    }

    /**
     * Create and send a request, retrying once if the authentication token is invalid.
     *
     * This method intentionally doesn't call requestAsyncWithRetry and wait()
     * on the promise, as we want to make sure the underlying sync client
     * methods are used.
     *
     * @param string                                $method  HTTP method
     * @param string|\Psr\Http\Message\UriInterface $uri     URI object or string.
     * @param array                                 $options Request options to apply.
     *
     * @return \Psr\Http\Message\ResponseInterface The response.
     */
    private function requestWithRetry(string $method, $uri, array $options)
    {
        try {
            $merged = $this->mergeAuth($options);

            return $this->client->request($method, $uri, $merged);
        } catch (ClientException $e) {
            // Only retry if MPX has returned that the existing token is no
            // longer valid.
            if (401 != $e->getCode()) {
                throw $e;
            }

            $merged = $this->mergeAuth($options, true);

            return $this->client->request($method, $uri, $merged);
        }
    }
}
