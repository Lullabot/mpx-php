<?php

namespace Lullabot\Mpx;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\HandlerStack;
use Lullabot\Mpx\Exception\ApiException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * An MPX API client.
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
     *                       must include Middleware::mpxErrors or a replacement.
     *
     * @return array An array of configuration options suitable for use with Guzzle.
     */
    public static function getDefaultConfiguration($handler = null)
    {
        $config = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];

        if (!$handler) {
            $handler = HandlerStack::create();
            $handler->push(Middleware::mpxErrors());
        }
        $config['handler'] = $handler;

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function request($method = 'GET', $url = null, array $options = [])
    {
        // MPX forces all JSON requests to return HTTP 200, even with an error.
        // We force all requests (including XML) to suppress errors so we can
        // have the same error handling code.
        $options['query']['httpError'] = false;

        return $this->client->request($method, $url, $options);
    }

    public function authenticatedRequest(User $user, $method = 'GET', $url = null, array $options = [])
    {
        // @todo Move this whole method to UserSession.
        if (isset($user)) {
            $duration = isset($options['timeout']) ? $options['timeout'] : null;
            $options['query']['token'] = (string) $user->acquireToken($duration);
        }

        try {
            return $this->request($method, $url, $options);
        } catch (ApiException $exception) {
            // If the token is invalid, we should delete it from storage so that a
            // fresh token is fetched on the next request.
            if (401 == $exception->getCode()) {
                // Ensure the token will be deleted.
                $user->invalidateToken();
            }
            throw $exception;
        }
    }

    /**
     * Handle an XML API response.
     *
     * @param ResponseInterface $response The response.
     * @param string            $url      The request URL.
     *
     * @throws \Lullabot\Mpx\Exception\ApiException If an error occurred.
     *
     * @return array The decoded response data.
     */
    protected function handleXmlResponse(ResponseInterface $response, $url)
    {
        if (false !== strpos((string) $response->getBody(), 'xmlns:e="http://xml.theplatform.com/exception"')) {
            if (preg_match('~<e:title>(.+)</e:title><e:description>(.+)</e:description><e:responseCode>(.+)</e:responseCode>~', (string) $response->getBody(), $matches)) {
                throw new ApiException("Error {$matches[1]} on request to {$url}: {$matches[2]}", (int) $matches[3]);
            } elseif (preg_match('~<title>(.+)</title><summary>(.+)</summary><e:responseCode>(.+)</e:responseCode>~', (string) $response->getBody(), $matches)) {
                throw new ApiException("Error {$matches[1]} on request to {$url}: {$matches[2]}", (int) $matches[3]);
            }
        }
        $data = simplexml_load_string($response->getBody()->getContents());
        $data = [$data->getName() => static::convertXmlToArray($data)];

        return $data;
    }

    /**
     * Convert a string of XML to an associative array.
     *
     * @param \SimpleXMLElement $data An XML element node.
     *
     * @return array An array representing the XML data.
     */
    protected static function convertXmlToArray(\SimpleXMLElement $data)
    {
        $result = [];
        foreach ((array) $data as $index => $node) {
            $result[$index] = (is_object($node)) ? static::convertXmlToArray($node) : trim((string) $node);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function send(RequestInterface $request, array $options = [])
    {
        return $this->client->send($request, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function sendAsync(RequestInterface $request, array $options = [])
    {
        return $this->client->sendAsync($request, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function requestAsync($method, $uri, array $options = [])
    {
        return $this->client->requestAsync($method, $uri, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig($option = null)
    {
        return $this->client->getConfig($option);
    }
}
