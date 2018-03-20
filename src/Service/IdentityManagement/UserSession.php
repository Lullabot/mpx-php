<?php

namespace Lullabot\Mpx\Service\IdentityManagement;

use GuzzleHttp\Promise\PromiseInterface;
use Lullabot\Mpx\Client;
use Lullabot\Mpx\Token;
use Lullabot\Mpx\TokenCachePool;
use Lullabot\Mpx\User;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\Promise;
use Lullabot\Mpx\Exception\ClientException;
use Lullabot\Mpx\Exception\TokenNotFoundException;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\StoreInterface;

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
     * The backend lock store used to store a lock when signing in to MPX.
     *
     * @var StoreInterface
     */
    protected $store;

    /**
     * Construct a new user session.
     *
     * Note that the session is not actually established until acquireToken is
     * called.
     *
     * @param \Lullabot\Mpx\Client         $client         The client used to access MPX.
     * @param \Lullabot\Mpx\User           $user           The user associated with this session.
     * @param StoreInterface               $store          The lock backend to store locks in.
     * @param \Lullabot\Mpx\TokenCachePool $tokenCachePool The cache of authentication tokens.
     * @param \Psr\Log\LoggerInterface     $logger         The logger used when logging automatic authentication renewals.
     *
     * @see \Psr\Log\NullLogger To disable logging within this session.
     */
    public function __construct(
        Client $client,
        User $user,
        StoreInterface $store,
        TokenCachePool $tokenCachePool,
        LoggerInterface $logger
    ) {
        $this->client = $client;
        $this->user = $user;
        $this->tokenCachePool = $tokenCachePool;
        $this->logger = $logger;
        $this->store = $store;
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
        $token = $this->acquireToken(null, $reset);
        $options['query'] += [
            'token' => $token->getValue(),
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
        $token = Token::fromResponseData($data);
        // Save the token to the cache and return it.
        $this->tokenCachePool->setToken($this->user, $token);

        return $token;
    }

    /**
     * Send a request, retrying once if the authentication token is invalid.
     *
     * @param \Psr\Http\Message\RequestInterface $request The request to send.
     * @param array                              $options Request options to apply.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface|\Psr\Http\Message\RequestInterface
     */
    private function sendAsyncWithRetry(RequestInterface $request, array $options)
    {
        // This is the initial API request that we expect to pass.
        $merged = $this->mergeAuth($options);
        $inner = $this->client->sendAsync($request, $merged);

        // However, if it fails, we need to try a second request. We can't
        // create the second request method outside of the promise body as we
        // need a new invocation of mergeAuth() that resets the token.
        $outer = $this->outerPromise($inner);

        $inner->then(function ($value) use ($outer) {
            // The very first request worked, so resolve the outer promise.
            $outer->resolve($value);
        }, function ($e) use ($request, $options, $outer) {
            // Only retry if it's a token auth error.
            if (!$this->isTokenAuthError($e)) {
                $outer->reject($e);

                return;
            }

            $merged = $this->mergeAuth($options, true);
            $func = [$this->client, 'send'];
            $args = [$request, $merged];
            $this->finallyResolve($outer, $func, $args);
        });

        return $outer;
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
            if (!$this->isTokenAuthError($e)) {
                throw $e;
            }

            $merged = $this->mergeAuth($options, true);

            return $this->client->send($request, $merged);
        }
    }

    /**
     * Create and send a request, retrying once if the authentication token is invalid.
     *
     * @param string                                $method  HTTP method
     * @param string|\Psr\Http\Message\UriInterface $uri     URI object or string.
     * @param array                                 $options Request options to apply.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface|\Psr\Http\Message\RequestInterface
     */
    private function requestAsyncWithRetry(string $method, $uri, array $options)
    {
        // This is the initial API request that we expect to pass.
        $merged = $this->mergeAuth($options);
        $inner = $this->client->requestAsync($method, $uri, $merged);

        // However, if it fails, we need to try a second request. We can't
        // create the second request method outside of the promise body as we
        // need a new invocation of mergeAuth() that resets the token.
        $outer = $this->outerPromise($inner);

        $inner->then(function ($value) use ($outer) {
            // The very first request worked, so resolve the outer promise.
            $outer->resolve($value);
        }, function ($e) use ($method, $uri, $options, $outer) {
            // Only retry if it's a token auth error.
            if (!$this->isTokenAuthError($e)) {
                $outer->reject($e);

                return;
            }

            $merged = $this->mergeAuth($options, true);
            $func = [$this->client, 'request'];
            $args = [$method, $uri, $merged];
            $this->finallyResolve($outer, $func, $args);
        });

        return $outer;
    }

    /**
     * Determine if an MPX exception is due to a token authentication failure.
     *
     * @param \Exception $e
     *
     * @return bool
     */
    private function isTokenAuthError(\Exception $e): bool
    {
        return ($e instanceof ClientException) && 401 == $e->getCode();
    }

    /**
     * Resolve or reject a promise by invoking a callable.
     *
     * @param \GuzzleHttp\Promise\PromiseInterface $promise
     * @param callable                             $callable
     * @param array                                $args
     */
    private function finallyResolve(PromiseInterface $promise, callable $callable, $args)
    {
        try {
            // Since we must have blocked to get to this point, we now use
            // a blocking request to resolve things once and for all.
            $promise->resolve(call_user_func_array($callable, $args));
        } catch (\Exception $e) {
            $promise->reject($e);
        }
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
            if (!$this->isTokenAuthError($e)) {
                throw $e;
            }

            $merged = $this->mergeAuth($options, true);

            return $this->client->request($method, $uri, $merged);
        }
    }

    /**
     * Return a new promise that waits on another promise.
     *
     * @param \GuzzleHttp\Promise\PromiseInterface $inner
     *
     * @return \GuzzleHttp\Promise\Promise
     */
    private function outerPromise(PromiseInterface $inner): Promise
    {
        $outer = new Promise(function () use ($inner) {
            // Our wait function invokes the inner's wait function, as as far
            // as callers are concerned there is only one promise.
            try {
                $inner->wait();
            } catch (\Exception $e) {
                // The inner promise handles all rejections.
            }
        });

        return $outer;
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
        $lock = $factory->createLock($this->user->getUsername(), 10);

        // Blocking means this will throw an exception on failure.
        $lock->acquire(true);

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
}
