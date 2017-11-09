<?php

namespace Mpx;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Mpx\Exception\ApiException;
use Psr\Http\Message\ResponseInterface;

class Client {

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    public function __construct(GuzzleClientInterface $client) {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function request($method = 'GET', $url = null, User $user = NULL, array $options = []) {
        $duration = isset($options['timeout']) ? $options['timeout'] : NULL;

        if (isset($user)) {
            $options['query']['token'] = (string) $user->acquireToken($duration);
        }

        // Disable HTTP error codes unless requested so that we can parse out
        // the errors ourselves.
        $options['query']['httpError'] = FALSE;

        try {
            $response = $this->client->request($method, $url, $options);
            return $this->handleResponse($response, $url);
        }
        catch (ApiException $exception) {
            // If the token is invalid, we should delete it from storage so that a
            // fresh token is fetched on the next request.
            if ($exception->getCode() == 401 && isset($user)) {
                // Ensure the token will be deleted.
                $user->invalidateToken();
            }
            throw $exception;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function handleResponse(ResponseInterface $response, $url) {
        // Parse out exceptions from the response body.
        $contentType = $response->getHeaderLine('Content-Type');
        if (preg_match('~^(application|text)/json~', $contentType)) {
            // @todo Figure out how we can support the big_int_string option here.
            // @see http://stackoverflow.com/questions/19520487/json-bigint-as-string-removed-in-php-5-5
            $data = \GuzzleHttp\json_decode($response->getBody()->getContents(), TRUE);
            if (!empty($data['responseCode']) && !empty($data['isException'])) {
                throw new ApiException("Error {$data['title']} on request to {$url}: {$data['description']}", (int) $data['responseCode']);
            }
            elseif (!empty($data[0]['entry']['responseCode']) && !empty($data[0]['entry']['isException'])) {
                throw new ApiException(
                    "Error {$data[0]['entry']['title']} on request to {$url}: {$data[0]['entry']['description']}",
                    (int) $data[0]['entry']['responseCode']
                );
            }
            return $data;
        }
        elseif (preg_match('~^(application|text)/(atom\+)?xml~', $contentType)) {
            if (strpos((string) $response->getBody(), 'xmlns:e="http://xml.theplatform.com/exception"') !== FALSE) {
                if (preg_match('~<e:title>(.+)</e:title><e:description>(.+)</e:description><e:responseCode>(.+)</e:responseCode>~', (string) $response->getBody(), $matches)) {
                    throw new ApiException("Error {$matches[1]} on request to {$url}: {$matches[2]}", (int) $matches[3]);
                }
                elseif (preg_match('~<title>(.+)</title><summary>(.+)</summary><e:responseCode>(.+)</e:responseCode>~', (string) $response->getBody(), $matches)) {
                    throw new ApiException("Error {$matches[1]} on request to {$url}: {$matches[2]}", (int) $matches[3]);
                }
            }
            $data = simplexml_load_string($response->getBody()->getContents());
            $data = array($data->getName() => static::convertXmlToArray($data));
            return $data;
        }
        else {
            throw new \RuntimeException("Unable to handle response with type $contentType.", $response);
        }
    }

    /**
     * Convert a string of XML to an associative array.
     *
     * @param \SimpleXMLElement $data
     *   An XML element node.
     *
     * @return array
     *   An array representing the XML data.
     */
    public static function convertXmlToArray(\SimpleXMLElement $data) {
        $result = array();
        foreach ((array) $data as $index => $node) {
            $result[$index] = (is_object($node)) ? static::convertXmlToArray($node) : trim(strval($node));
        }
        return $result;
    }

}
