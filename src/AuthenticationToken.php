<?php

namespace Mpx;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class AuthenticationToken {

  /**
   * @var \Mpx/Account
   */
  private $account;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  private $logger;

  /**
   * @var string
   */
  private $token;

  /**
   * @var int
   */
  private $expires;

  public function __construct(LoggerInterface $logger, Account $account, $token = NULL, $expires = NULL) {
    $this->logger = $logger;
    $this->account = $account;
    if (!empty($token) && !empty($expires)) {
      $this->token = $token;
      $this->expires = $expires;
    }
  }

  public function __toString() {
    return $this->token;
  }

  public function setToken($token) {
    $this->token = $token;
  }

  public function setExpires($expires) {
    $this->expires = $expires;
  }

  public function isValid() {
    return !empty($this->token) && time() < $this->expires;
  }

  /**
   * Fetchs the authentication token.
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
    $this->token = $json['signInResponse']['token'];
    $this->expires = $time + ($json['signInResponse']['duration'] / 1000);

    if (!$this->isValid()) {
      throw new \Exception("New MPX authentication token for {$this->account->getUsername()} is already invalid.");
    }
    else {
      $duration = $this->expires - $time;
      $this->logger->info("New MPX authentication token fetched for {$this->account->getUsername()}, valid for $duration seconds.");
    }

    return $this;
  }

}
