<?php

namespace Lullabot\Mpx;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * An mpx API client.
 */
class Client implements GuzzleClientInterface
{
    /**
     * The underlying HTTP client.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * Client constructor.
     *
     * Custom client implementations should include the Middleware
     * handler, otherwise MPX errors may not be exposed correctly.
     *
     * @see \Lullabot\Mpx\Client::getDefaultConfiguration
     * @see \Lullabot\Mpx\Middleware
     *
     * @param \GuzzleHttp\ClientInterface $client The underlying HTTP client to use for requests.
     */
    public function __construct(GuzzleClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Get the default Guzzle client configuration array.
     *
     * @param mixed $handler (optional) A Guzzle handler to use for
     *                       requests. If a custom handler is specified, it must
     *                       include Middleware::mpxErrors or a replacement.
     *
     * @return array An array of configuration options suitable for use with Guzzle.
     */
    public static function getDefaultConfiguration(mixed $handler = null)
    {
        $config = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];

        if (!$handler) {
            $handler = HandlerStack::create();
            $handler->push(Middleware::mpxErrors(), 'mpx_errors');
        }
        $config['handler'] = $handler;

        return $config;
    }

    public function request($method = 'GET', $url = null, array $options = []): ResponseInterface
    {
        // MPX forces all JSON requests to return HTTP 200, even with an error.
        // We force all requests (including XML) to suppress errors so we can
        // have the same error handling code.
        $options['query']['httpError'] = false;

        return $this->client->request($method, $url, $options);
    }

    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        return $this->client->send($request, $options);
    }

    public function sendAsync(RequestInterface $request, array $options = []): PromiseInterface
    {
        return $this->client->sendAsync($request, $options);
    }

    public function requestAsync($method, $uri, array $options = []): PromiseInterface
    {
        return $this->client->requestAsync($method, $uri, $options);
    }

    public function getConfig($option = null)
    {
        return $this->client->getConfig($option);
    }
}
