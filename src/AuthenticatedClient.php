<?php

namespace Lullabot\Mpx;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use Lullabot\Mpx\DataService\IdInterface;
use Lullabot\Mpx\Exception\ClientException;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AuthenticatedClient implements ClientInterface
{
    /**
     * The user to establish a client for.
     *
     * @var \Lullabot\Mpx\Service\IdentityManagement\UserSession
     */
    protected $userSession;

    /**
     * The client used to access MPX.
     *
     * @var \Lullabot\Mpx\Client
     */
    protected $client;

    /**
     * An optional account to use as the account context for requests.
     *
     * @see https://docs.theplatform.com/help/wsf-introduction-to-theplatforms-web-services#IntroductiontothePlatform%27sWebservices-Accountcontext
     *
     * @var \Lullabot\Mpx\DataService\IdInterface
     */
    protected $account;

    /**
     * The duration for authentication tokens to last for, or null for no
     * specific expiry.
     *
     * @var int
     */
    protected $duration;

    /**
     * Construct a new AuthenticatedClient.
     *
     * Note that the authentication is not actually established until
     * acquireToken is called.
     *
     * @param Client      $client      The client used to access MPX.
     * @param UserSession $userSession The user associated with this client.
     * @param IdInterface $account     (optional) The account to use as the account context for requests.
     */
    public function __construct(Client $client, UserSession $userSession, ?IdInterface $account = null)
    {
        $this->client = $client;
        $this->userSession = $userSession;

        if ($account) {
            $this->account = $account;
        }
    }

    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        return $this->sendWithRetry($request, $options);
    }

    public function sendAsync(RequestInterface $request, array $options = []): PromiseInterface
    {
        return $this->sendAsyncWithRetry($request, $options);
    }

    public function request($method, $uri, array $options = []): ResponseInterface
    {
        return $this->requestWithRetry($method, $uri, $options);
    }

    public function requestAsync($method, $uri, array $options = []): PromiseInterface
    {
        return $this->requestAsyncWithRetry($method, $uri, $options);
    }

    public function getConfig($option = null)
    {
        return $this->client->getConfig($option);
    }

    /**
     * Set the duration for token lifetimes.
     *
     * @param int|null $duration The duration in seconds, or null to not use a specific lifetime.
     */
    public function setTokenDuration(?int $duration = null): void
    {
        $this->duration = $duration;
    }

    /**
     * Return if this client has an account context.
     */
    public function hasAccount(): bool
    {
        return isset($this->account);
    }

    /**
     * Return the account to use as the context for requests.
     *
     * @throws \LogicException Thrown if an account context is not set.
     */
    public function getAccount(): IdInterface
    {
        if (!$this->account) {
            throw new \LogicException('hasAccount() must return TRUE before calling getAccount()');
        }

        return $this->account;
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
        $token = $this->userSession->acquireToken($this->duration, $reset);
        $options['query'] += [
            'token' => $token->getValue(),
        ];

        return $options;
    }

    /**
     * Send a request, retrying once if the authentication token is invalid.
     *
     * @param RequestInterface $request The request to send.
     * @param array            $options Request options to apply.
     */
    private function sendAsyncWithRetry(RequestInterface $request, array $options): PromiseInterface|RequestInterface
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
            $func = $this->client->send(...);
            $args = [$request, $merged];
            $this->finallyResolve($outer, $func, $args);
        });

        return $outer;
    }

    /**
     * Send a request, retrying once if the authentication token is invalid.
     *
     * @param RequestInterface $request The request to send.
     * @param array            $options An array of request options.
     *
     * @return ResponseInterface
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
     */
    private function requestAsyncWithRetry(string $method, string|\Psr\Http\Message\UriInterface $uri, array $options): PromiseInterface|RequestInterface
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
            $func = $this->client->request(...);
            $args = [$method, $uri, $merged];
            $this->finallyResolve($outer, $func, $args);
        });

        return $outer;
    }

    /**
     * Determine if an MPX exception is due to a token authentication failure.
     */
    private function isTokenAuthError(\Exception $e): bool
    {
        return ($e instanceof ClientException) && 401 == $e->getCode();
    }

    /**
     * Resolve or reject a promise by invoking a callable.
     */
    private function finallyResolve(PromiseInterface $promise, callable $callable, $args)
    {
        try {
            // Since we must have blocked to get to this point, we now use
            // a blocking request to resolve things once and for all.
            $promise->resolve(\call_user_func_array($callable, $args));
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
     * @return ResponseInterface The response.
     */
    private function requestWithRetry(string $method, string|\Psr\Http\Message\UriInterface $uri, array $options)
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
     */
    private function outerPromise(PromiseInterface $inner): Promise
    {
        $outer = new Promise(function () use ($inner) {
            // Our wait function invokes the inner's wait function, as as far
            // as callers are concerned there is only one promise.
            try {
                $inner->wait();
            } catch (\Exception) {
                // The inner promise handles all rejections.
            }
        });

        return $outer;
    }
}
