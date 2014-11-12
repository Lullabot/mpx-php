<?php

namespace Mpx;

use GuzzleHttp\Client;

class AuthenticationToken {

  /**
   * @var MPX/AccountInterface
   */
  private $account;

  /**
   * @var string
   */
  private $token;

  /**
   * @var int
   */
  private $timeout;

  public function __construct(Account $account, $token = NULL, $timeout = 3000) {
    $this->account = $account;

    if (empty($token)) {
      $this->fetch();
    }
    else {
      $this->token = $token;
      $this->timeout = $timeout;
    }
  }

  public function __toString() {
    return $this->token;
  }

  public function isValid() {
    return !empty($this->token) && time() < $this->timeout;
  }

  public function fetch($timeout = NULL) {
    if (!isset($timeout)) {
      $timeout = $this->timeout;
    }

    $client = new Client();
    $options = array();
    $options['body'] = array('username' => $this->account->username, 'password' => $this->account->password);
    $options['query'] = array('schema' => '1.0', 'form' => 'json', '_idleTimeout' => $timeout);
    $response = $client->post('https://identity.auth.theplatform.com/idm/web/Authentication/signIn', $options);
    $json = $response->json();
    $this->token = $json['signInResponse']['token'];
    $this->timeout = $json['signInResponse']['idleTimeout'];
  }

}
