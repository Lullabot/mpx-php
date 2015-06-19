<?php

/**
 * @file
 * Contains Mpx\TokenServiceBase.
 */

namespace Mpx;

use Pimple\Container;
use Psr\Log\LoggerInterface;

/**
 * Provides a base class for all TokenServiceInterface classes.
 */
abstract class TokenServiceBase implements TokenServiceInterface {
  use ClientTrait;
  use LoggerTrait;

  /**
   * @param \Mpx\ClientInterface $client
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function __construct(ClientInterface $client = NULL, LoggerInterface $logger = NULL) {
    $this->client = $client;
    $this->logger = $logger;
  }

  /**
   * @param \Pimple\Container $container
   *
   * @return static
   *
   * @todo How can we ensure this always returns the same instance?
   */
  public static function create(Container $container) {
    return new static(
      $container['client'],
      $container['logger']
    );
  }

  /**
   * {@inheritdoc}
   */
  public function delete(TokenInterface $token) {
    // If the token is still valid, expire it using the API.
    if ($token->isValid()) {
      $this->expire($token);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function fetch($username, $password, $duration = NULL) {
    $options['auth'] = array($username, $password);
    $options['query'] = array(
      'schema' => '1.0',
      'form' => 'json',
    );

    if (!empty($duration)) {
      // API expects this value in milliseconds, not seconds.
      $options['query']['_duration'] = $duration * 1000;
      $options['query']['_idleTimeout'] = $duration * 1000;
    }

    $time = time();
    $response = $this->client()->get(
      'https://identity.auth.theplatform.com/idm/web/Authentication/signIn',
      $options
    );
    $data = $response->json();

    $token = $data['signInResponse']['token'];
    $lifetime = floor(min($data['signInResponse']['duration'], $data['signInResponse']['idleTimeout']) / 1000);
    $expires = $time + $lifetime;

    $this->logger()->info(
      'Fetched new mpx token {token} for user {username} that expires on {date}.',
      array(
        'token' => $token,
        'username' => $username,
        'date' => date(DATE_ISO8601, $expires),
      )
    );

    return new Token($username, $token, $expires);
  }

  /**
   * {@inheritdoc}
   */
  public function expire(TokenInterface $token) {
    $this->client()->get(
      'https://identity.auth.theplatform.com/idm/web/Authentication/signOut',
      array(
        'query' => array(
          'schema' => '1.0',
          'form' => 'json',
          '_token' => $token->getValue(),
        ),
      )
    );

    $this->logger()->info(
      'Expired mpx authentication token {token} for {username}.',
      array(
        'token' => $token->getValue(),
        'username' => $token->getUsername(),
      )
    );

    unset($token);
  }

}
