<?php

namespace Mpx;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Pimple\Container;

class Account {
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
   * @var Container
   */
  private $container;

  /**
   * @param $username
   * @param $password
   * @param \Pimple\Container $container
   */
  public function __construct($username, $password, Container $container) {
    $this->username = $username;
    $this->password = $password;
    // The dependency injection container contains a logger instance and an HTTP
    // client factory.
    $this->container = $container;
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
   * @throws \Exception
   */
  public function setToken($token, $expires = NULL) {
    $this->token = $token;
    if (isset($expires)) {
      $this->expires = $expires;
    }
    if (!$this->isTokenValid()) {
      throw new \Exception("Invalid MPX authentication token {$token} for {$this->getUsername()}.");
    }
    return $this;
  }

  /**
   * Gets a current authentication token for the account.
   *
   * @return string
   */
  public function getToken() {
    return $this->token;
  }

  public function checkToken() {
    if (!$this->getToken()) {
      $this->signIn();
    }
    elseif (!$this->isTokenValid()) {
      $this->logger->info("Invalid MPX authentication token {token} for {username}. Fetching new token.", array('token' => $this->token, 'username' => $this->getUsername()));
      $this->signOut();
      $this->signIn();
    }
    return $this->getToken();
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
   * @return \Psr\Log\LoggerInterface
   */
  public function getLogger() {
    if (!isset($this->logger)) {
      $this->logger = $this->container['logger'];
    }
    return $this->logger;
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
   * @throws \Exception
   */
  public function signIn($duration = NULL, $idleTimeout = NULL) {
    $client = $this->container['client'];
    $options = array();
    $options['body'] = array('username' => $this->getUsername(), 'password' => $this->getPassword());
    $options['query'] = array('schema' => '1.0', 'form' => 'json', 'httpError' => 'true');
    if (!empty($duration)) {
      $options['query']['_duration'] = $duration;
    }
    if (!empty($idleTimeout)) {
      $options['query']['_idleTimeout'] = $idleTimeout;
    }
    $time = time();
    $response = $client->post('https://identity.auth.theplatform.com/idm/web/Authentication/signIn', $options);
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
    if ($token = $this->getToken()) {
      $client = $this->container['client'];
      $options = array();
      $options['query'] = array('schema' => '1.0', 'form' => 'json', 'httpError' => 'true', '_token' => $token);
      $client->get('https://identity.auth.theplatform.com/idm/web/Authentication/signOut', $options);
      $this->token = NULL;
      $this->expires = NULL;
      $this->logger->info("Expired MPX token {token} for {username}.", array('token' => $token, 'username' => $this->getUsername()));
    }
  }

}
