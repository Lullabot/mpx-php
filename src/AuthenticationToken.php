<?php

namespace Mpx;

use GuzzleHttp\Client;

class AuthenticationToken {

  /**
   * @var \Mpx\Account
   */
  private $account;

  /**
   * @var string
   */
  private $token;

  /**
   * @var int
   */
  private $expires;

  public function __construct(Account $account, $token = NULL, $expires = NULL) {
    $this->account = $account;

    if (!empty($token) && !empty($expires)) {
      $this->token = $token;
      $this->expires = $expires;
    }
  }

  /**
   * @return string
   */
  public function __toString() {
    return (string) $this->token;
  }

  public function getToken() {
    return $this->token;
  }

  public function setToken($token) {
    $this->token = $token;
  }

  public function getExpires() {
    return $this->expires;
  }

  public function setExpires($expires) {
    $this->expires = $expires;
  }

  public function isValid() {
    return !empty($this->token) && time() < $this->expires;
  }

  /**
   * Fetches the authentication token.
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
  public function fetch($duration = NULL, $idleTimeout = NULL) {
    $client = new Client();
    $options = array();
    $options['body'] = array('username' => $this->account->getUsername(), 'password' => $this->account->getPassword());
    $options['query'] = array('schema' => '1.0', 'form' => 'json', 'httpError' => 'true');
    if (isset($duration)) {
      $options['query']['_duration'] = $duration;
    }
    if (isset($idleTimeout)) {
      $options['query']['_idleTimeout'] = $idleTimeout;
    }
    $time = time();
    $response = $client->post('https://identity.auth.theplatform.com/idm/web/Authentication/signIn', $options);
    $json = $response->json();
    $this->token = (string) $json['signInResponse']['token'];
    $this->expires = $time + (min($json['signInResponse']['duration'], $json['signInResponse']['idleTimeout']) / 1000);

    if (!$this->isValid()) {
      throw new \Exception("New MPX authentication token {$this->token} for {$this->account->getUsername()} is already invalid.");
    }
    else {
      $this->account->getLogger()->info("New MPX authentication token {token} fetched for {username}, valid for {duration} seconds.", array('token' => $this->token, 'username' => $this->account->getUsername(), 'duration' => $this->expires - $time));
    }

    return $this;
  }

}
