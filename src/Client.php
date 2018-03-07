<?php

namespace Lullabot\Mpx;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\HandlerStack;
use Lullabot\Mpx\Exception\ApiException;
use Psr\Http\Message\ResponseInterface;

class Client implements GuzzleClientInterface
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    public function __construct(GuzzleClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Get the default Guzzle client configuration array.
     *
     * @param mixed $handler
     *   (optional) Specify a Guzzle handler to use for requests.
     *
     * @return array
     *   An array of configuration options suitable for use with Guzzle.
     */
    public static function getDefaultConfiguration($handler = null) {
        $config = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];

        if (!$handler) {
            $handler = HandlerStack::create();
        }
        $handler->push(HttpErrorMiddleware::invoke());
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
        $contentType = $response->getHeaderLine('Content-Type');
        if (preg_match('~^(application|text)/json~', $contentType)) {
            return $this->handleJsonResponse($response, $url);
        } elseif (preg_match('~^(application|text)/(atom\+)?xml~', $contentType)) {
            // @todo Are there APIs that are XML only?
            return $this->handleXmlResponse($response, $url);
        } else {
            //throw new ApiException("Unable to handle response from {$url} with content type {$contentType}.");
        }
    }

    public function authenticatedRequest(User $user, $method = 'GET', $url = null, array $options = [])
    {
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
     * Handle an JSON API response.
     *
     * @param ResponseInterface $response The response.
     * @param string            $url      The request URL.
     *
     * @throws \Lullabot\Mpx\Exception\ApiException If an error occurred.
     *
     * @return array The decoded response data.
     */
    protected function handleJsonResponse(ResponseInterface $response, $url)
    {
        // @todo Figure out how we can support the big_int_string option here.
        // @see http://stackoverflow.com/questions/19520487/json-bigint-as-string-removed-in-php-5-5
        $data = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
        if (!empty($data['responseCode']) && !empty($data['isException'])) {
            throw new ApiException("Error {$data['title']} on request to {$url}: {$data['description']}", (int) $data['responseCode']);
        } elseif (!empty($data[0]['entry']['responseCode']) && !empty($data[0]['entry']['isException'])) {
            throw new ApiException(
                "Error {$data[0]['entry']['title']} on request to {$url}: {$data[0]['entry']['description']}",
                (int) $data[0]['entry']['responseCode']
            );
        }

        return $data;
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
     * Send an HTTP request.
     *
     * @param \Psr\Http\Message\RequestInterface $request Request to send
     * @param array $options Request options to apply to the given
     *                                  request and to the transfer.
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(\Psr\Http\Message\RequestInterface $request, array $options = []) {
        // TODO: Implement send() method.
    }

    /**
     * Asynchronously send an HTTP request.
     *
     * @param \Psr\Http\Message\RequestInterface $request Request to send
     * @param array $options Request options to apply to the given
     *                                  request and to the transfer.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function sendAsync(\Psr\Http\Message\RequestInterface $request, array $options = []) {
        // TODO: Implement sendAsync() method.
    }

    /**
     * Create and send an asynchronous HTTP request.
     *
     * Use an absolute path to override the base path of the client, or a
     * relative path to append to the base path of the client. The URL can
     * contain the query string as well. Use an array to provide a URL
     * template and additional variables to use in the URL template expansion.
     *
     * @param string $method HTTP method
     * @param string|\Psr\Http\Message\UriInterface $uri URI object or string.
     * @param array $options Request options to apply.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function requestAsync($method, $uri, array $options = []) {
        // TODO: Implement requestAsync() method.
    }

    /**
     * Get a client configuration option.
     *
     * These options include default request options of the client, a "handler"
     * (if utilized by the concrete client), and a "base_uri" if utilized by
     * the concrete client.
     *
     * @param string|null $option The config option to retrieve.
     *
     * @return mixed
     */
    public function getConfig($option = NULL) {
        // TODO: Implement getConfig() method.
    }
}
