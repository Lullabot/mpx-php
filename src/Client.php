<?php

/**
 * @file
 * Contains Mpx\Client.
 */

namespace Mpx;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Message\RequestInterface;
use Mpx\Exception\ApiException;

class Client extends GuzzleClient implements ClientInterface {

  /**
   * {@inheritdoc}
   */
  public function authenticatedGet(UserInterface $user, $url = null, $options = []) {
    $duration = isset($options['timeout']) ? $options['timeout'] : NULL;
    $token = $user->acquireToken($duration);
    $options['query']['token'] = $token->getValue();

    try {
      $response = $this->get($url, $options);
      return $response;
    }
    catch (ApiException $exception) {
      // If the token is invalid, we should delete it from storage so that a
      // fresh token is fetched on the next request.
      if ($exception->getCode() == 401) {
        // Flag the token as expired so it will not be reused.
        $token->invalidate();
        // Ensure the token will be deleted.
        //register_shutdown_function(array($user->tokenService(), 'delete'), $options['query']['token']);
      }
      throw $exception;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function send(RequestInterface $request) {
    $response = parent::send($request);

    // Parse out JSON exceptions from the API.
    if ($response->getHeader('Content-Type') && preg_match('~^(application|text)/json~', $response->getHeader('Content-Type'))) {
      $data = $response->json();
      if (!empty($data['responseCode']) && !empty($data['isException'])) {
        throw new ApiException("Error {$data['title']} on request to {$request->getUrl()}: {$data['description']}", (int) $data['responseCode']);
      }
      elseif (!empty($data[0]['entry']['responseCode']) && !empty($data[0]['entry']['isException'])) {
        throw new ApiException("Error {$data[0]['entry']['title']} on request to {$request->getUrl()}: {$data[0]['entry']['description']}", (int) $data[0]['entry']['responseCode']);
      }
    }

    return $response;
  }

}
