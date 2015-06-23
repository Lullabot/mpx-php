<?php

/**
 * @file
 * Contains Mpx\Client.
 */

namespace Mpx;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Message\RequestInterface;
use Mpx\Exception\ApiException;
use GuzzleHttp\Exception\ParseException;

class Client extends GuzzleClient implements ClientInterface {

  /**
   * {@inheritdoc}
   */
  public function authenticatedGet(UserInterface $user, $url = null, $options = []) {
    $duration = isset($options['timeout']) ? $options['timeout'] : NULL;
    $options['query']['token'] = $user->acquireToken($duration);

    try {
      $response = $this->get($url, $options);
      return $response;
    }
    catch (ApiException $exception) {
      // If the token is invalid, we should delete it from storage so that a
      // fresh token is fetched on the next request.
      if ($exception->getCode() == 401) {
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
  public function send(RequestInterface $request) {
    // Disable HTTP error codes unless requested so that we can parse out
    // the errors ourselves.
    if (!$request->getQuery()->hasKey('httpError')) {
      $request->getQuery()->set('httpError', 'false');
    }

    $response = parent::send($request);

    // Parse out exceptions from the response body.
    $contentType = $response->getHeader('Content-Type');
    if (preg_match('~^(application|text)/json~', $contentType)) {
      $data = $response->json();
      if (!empty($data['responseCode']) && !empty($data['isException'])) {
        throw new ApiException("Error {$data['title']} on request to {$request->getUrl()}: {$data['description']}", (int) $data['responseCode']);
      }
      elseif (!empty($data[0]['entry']['responseCode']) && !empty($data[0]['entry']['isException'])) {
        throw new ApiException(
          "Error {$data[0]['entry']['title']} on request to {$request->getUrl()}: {$data[0]['entry']['description']}",
          (int) $data[0]['entry']['responseCode']
        );
      }
      return $data;
    }
    elseif (preg_match('~^(application|text)/xml~', $contentType)) {
      if (preg_match('~<e:exception xmlns:e="http://xml.theplatform.com/exception"><e:title>(.+)</e:title><e:description>(.+)</e:description><e:responseCode>(.+)</e:responseCode>~', $response->getBody(), $matches)) {
        throw new ApiException("Error {$matches[1]} on request to {$request->getUrl()}: {$matches[2]}", (int) $matches[3]);
      }
      $data = $response->xml();
      $data = array($data->getName() => static::convertXmlToArray($data));
      return $data;
    }
    else {
      throw new ParseException("Unable to handle response with type $contentType.", $response);
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
