<?php

namespace Mpx;

use GuzzleHttp\Client;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

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
   * The duration of the token, in milliseconds.
   *
   * @var int
   */
  private $duration;

  /**
   * The idle timeout for the token, in milliseconds.
   *
   * @var int
   */
  private $idleTimeout;

  public function __construct($username, $password, $token = NULL, $expires = NULL) {
    $this->username = $username;
    $this->password = $password;
    $this->token = $token;
    $this->expires = $expires;
  }

  /**
   * @param string $token
   * @param int $expires
   *
   * @return $this
   */
  public function setToken($token, $expires = NULL) {
    $this->token = $token;
    if (isset($expires)) {
      $this->expires = $expires;
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
      $this->logger->info("Expired or invalid MPX token {token} for {username}. Fetching new token.", array('token' => $this->token, 'username' => $this->getUsername()));
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

  public function setDuration($duration) {
    $this->duration = $duration;
  }

  public function getDuration() {
    return $this->duration;
  }

  public function setIdleTimeout($idleTimeout) {
    $this->idleTimeout = $idleTimeout;
  }

  public function getIdleTimeout() {
    return $this->idleTimeout;
  }

  /**
   * @return \Psr\Log\LoggerInterface
   */
  public function getLogger() {
    if (!isset($this->logger)) {
      $this->logger = new NullLogger();
    }
    return $this->logger;
  }

  /**
   * Signs in the user.
   *
   * @return $this
   *
   * @throws \Exception
   */
  public function signIn() {
    $client = new Client();
    $options = array();
    $options['body'] = array('username' => $this->getUsername(), 'password' => $this->getPassword());
    $options['query'] = array('schema' => '1.0', 'form' => 'json', 'httpError' => 'true');
    if ($duration = $this->getDuration()) {
      $options['query']['_duration'] = $duration;
    }
    if ($idleTimeout = $this->getIdleTimeout()) {
      $options['query']['_idleTimeout'] = $idleTimeout;
    }
    $time = time();
    $response = $client->post('https://identity.auth.theplatform.com/idm/web/Authentication/signIn', $options);
    $json = $response->json();
    var_export($json);

    $token = $json['signInResponse']['token'];
    $expires = $time + (min($json['signInResponse']['duration'], $json['signInResponse']['idleTimeout']) / 1000);
    $this->setToken($token, $expires);

    if (!$this->isTokenValid()) {
      throw new \Exception("New MPX authentication token {$token} for {$this->getUsername()} is already invalid.");
    }
    else {
      $this->logger->info("New MPX authentication token {token} fetched for {username}, valid for {duration} seconds.", array('token' => $token, 'username' => $this->getUsername(), 'duration' => $expires - $time));
    }
  }

  /**
   * Signs out the user.
   */
  public function signOut() {
    if ($token = $this->getToken()) {
      $client = new Client();
      $options = array();
      $options['query'] = array('schema' => '1.0', 'form' => 'json', 'httpError' => 'true', '_token' => $token);
      $client->get('https://identity.auth.theplatform.com/idm/web/Authentication/signOut', $options);
      $this->token = NULL;
      $this->expires = NULL;
      $this->logger->info("Expired MPX token {token} for {username}.", array('token' => $token, 'username' => $this->getUsername()));
    }
  }

}
