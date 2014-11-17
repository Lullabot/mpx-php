<?php

/**
 * @file
 * Contains Mpx\Account.
 */

namespace Mpx;

use Pimple\Container;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class Account {
  use ClientTrait;
  use LoggerAwareTrait;

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
   * @param string $username
   * @param string $password
   * @param \GuzzleHttp\ClientInterface $client
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function __construct($username, $password, ClientInterface $client, LoggerInterface $logger) {
    $this->username = $username;
    $this->password = $password;
    $this->setClient($client);
    $this->setLogger($logger);
  }

  /**
   * @param string $username
   * @param string $password
   * @param \Pimple\Container $container
   * @return $this
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
   * @return string
   */
  public function getUsername() {
    return $this->username;
  }

  /**
   * @return string
   */
  public function getPassword() {
    return $this->password;
  }

  /**
   * @param string $token
   * @param int $expires
   *
   * @return $this
   *
   * @throws Exception
   */
  public function setToken($token, $expires = NULL) {
    $this->token = $token;
    if (isset($expires)) {
      $this->expires = $expires;
    }
    if (!$this->isTokenValid()) {
      throw new Exception("Invalid MPX authentication token {$token} for {$this->getUsername()}.");
    }
    return $this;
  }

  /**
   * Gets a current authentication token for the account.
   *
   * @param bool $fetch
   *   TRUE if a new token should be fetched if one is not available.
   *
   * @return string
   */
  public function getToken($fetch = TRUE) {
    if ($fetch) {
      if (!$this->token) {
        $this->signIn();
      }
      elseif (!$this->isTokenValid()) {
        $this->logger->info("Invalid MPX authentication token {token} for {username}. Fetching new token.", array('token' => $this->token, 'username' => $this->getUsername()));
        $this->signOut();
        $this->signIn();
      }
    }
    return $this->token;
  }

  public function isTokenValid() {
    return !empty($this->token) && !empty($this->expires) && time() < $this->expires;
  }

  public function setExpires($expires) {
    $this->expires = $expires;
  }

  public function getExpires() {
    return $this->expires;
  }

  /**
   * Signs in the user.
   *
   * @param int $duration
   *   The duration of the token, in milliseconds.
   * @param int $idleTimeout
   *   The idle timeout for the token, in milliseconds.
   *
   * @return $this
   *
   * @throws Exception
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
    $response = $this->client->post('https://identity.auth.theplatform.com/idm/web/Authentication/signIn', $options);
    $json = $response->json();

    $token = $json['signInResponse']['token'];
    $expires = $time + (min($json['signInResponse']['duration'], $json['signInResponse']['idleTimeout']) / 1000);
    $this->setToken($token, $expires);
    $this->logger->info("New MPX authentication token {token} fetched for {username}, valid for {duration} seconds.", array('token' => $token, 'username' => $this->getUsername(), 'duration' => $expires - $time));
  }

  /**
   * Signs out the user.
   */
  public function signOut() {
    if ($token = $this->getToken(FALSE)) {
      $options = array();
      $options['query'] = array('_token' => $token);
      $this->client->get('https://identity.auth.theplatform.com/idm/web/Authentication/signOut', $options);
      $this->token = NULL;
      $this->expires = NULL;
      $this->logger->info("Expired MPX token {token} for {username}.", array('token' => $token, 'username' => $this->getUsername()));
    }
  }

}
