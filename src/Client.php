<?php

namespace Lullabot\Mpx;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Lullabot\Mpx\Exception\ApiException;
use Psr\Http\Message\ResponseInterface;

class Client
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    public function __construct(GuzzleClientInterface $client)
    {
        $this->client = $client;
    }

    public function request($method = 'GET', $url = null, array $options = [])
    {
        // Disable HTTP error codes unless requested so that we can parse out
        // the errors ourselves.
        $options['query']['httpError'] = false;

        $response = $this->client->request($method, $url, $options);
        $contentType = $response->getHeaderLine('Content-Type');
        if (preg_match('~^(application|text)/json~', $contentType)) {
            return $this->handleJsonResponse($response, $url);
        } elseif (preg_match('~^(application|text)/(atom\+)?xml~', $contentType)) {
            return $this->handleXmlResponse($response, $url);
        } else {
            throw new ApiException("Unable to handle response from {$url} with content type {$contentType}.");
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
}
