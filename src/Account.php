<?php

/**
 * @file
 * Contains Mpx\Account.
 */

namespace Mpx;

use Mpx\Exception\InvalidTokenException;
use GuzzleHttp\ClientInterface;
use Pimple\Container;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class Account implements AccountInterface {
  use ClientTrait;
  use LoggerAwareTrait;

  const SIGNIN_URL = 'https://identity.auth.theplatform.com/idm/web/Authentication/signIn';
  const SIGNOUT_URL = 'https://identity.auth.theplatform.com/idm/web/Authentication/signOut';

  /**
   * @var string
   */
  private $username;

  /**
   * @var string
   */
  private $password;

  /**
   * @var string
   */
  private $token;

  /**
   * @var int
   */
  private $expires;

  /**
   * {@inheritdoc}
   */
  public function __construct($username, $password, ClientInterface $client, LoggerInterface $logger) {
    $this->username = $username;
    $this->password = $password;
    $this->setClient($client);
    $this->setLogger($logger);
  }

  /**
   * {@inheritdoc}
   */
  public static function create($username, $password, Container $container) {
    return new static(
      $username,
      $password,
      $container['client'],
      $container['logger']
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getUsername() {
    return $this->username;
  }

  /**
   * {@inheritdoc}
   */
  public function getPassword() {
    return $this->password;
  }

  /**
   * {@inheritdoc}
   */
  public function setToken($token, $expires) {
    $this->token = $token;
    $this->expires = $expires;
  }

  /**
   * {@inheritdoc}
   */
  public function getToken($fetch = TRUE) {
    if ($fetch) {
      if (!$this->token) {
        $this->signIn();
      }
      elseif (!static::isTokenValid($this->token, $this->expires)) {
        $this->logger->info("Invalid MPX authentication token {token} for {username}. Fetching new token.", array('token' => $this->token, 'username' => $this->getUsername()));
        $this->signOut();
        $this->signIn();
      }
    }
    return $this->token;
  }

  public function hasValidToken() {
    return static::isTokenValid($this->getToken(), $this->getExpires());
  }

  public function getValidToken() {
    if (!$this->getToken()) {
      $this->signIn();
    }
    elseif (!$this->hasValidToken()) {
      $this->logger->info("Invalid MPX authentication token {token} for {username}. Fetching new token.", array('token' => $this->getToken(), 'username' => $this->getUsername()));
      $this->signOut();
      $this->signIn();
    }
    return $this->getToken();
  }

  /**
   * {@inheritdoc}
   */
  public static function isValidToken($token, $expires) {
    return !empty($token) && !empty($expires) && is_numeric($expires) && time() < $expires;
  }

  /**
   * {@inheritdoc}
   */
  public function setExpires($expires) {
    $this->expires = $expires;
  }

  /**
   * {@inheritdoc}
   */
  public function getExpires() {
    return $this->expires;
  }

  /**
   * {@inheritdoc}
   */
  public function signIn($duration = NULL, $idleTimeout = NULL) {
    $options = array();
    $options['body'] = array('username' => $this->getUsername(), 'password' => $this->getPassword());
    if (!empty($duration)) {
      $options['query']['_duration'] = $duration;
    }
    if (!empty($idleTimeout)) {
      $options['query']['_idleTimeout'] = $idleTimeout;
    }
    $time = time();
    $response = $this->client->post(static::SIGNIN_URL, $options);
    $json = $response->json();

    $token = $json['signInResponse']['token'];
    $expires = $time + (min($json['signInResponse']['duration'], $json['signInResponse']['idleTimeout']) / 1000);

    if (!static::isValidToken($token, $expires)) {
      throw new InvalidTokenException("New MPX authentication token {$token} requested for {$this->getUsername()} is already invalid.");
    }

    $this->setToken($token, $expires);
    $this->logger->info("New MPX authentication token {token} fetched for {username}, valid for {duration} seconds.", array('token' => $token, 'username' => $this->getUsername(), 'duration' => $expires - $time));
  }

  /**
   * {@inheritdoc}
   */
  public function signOut() {
    if ($token = $this->getToken(FALSE)) {
      $options = array();
      $options['query'] = array('_token' => $token);
      $this->client->get(static::SIGNOUT_URL, $options);
      $this->token = NULL;
      $this->expires = NULL;
      $this->logger->info("Expired MPX token {token} for {username}.", array('token' => $token, 'username' => $this->getUsername()));
    }
  }
}
