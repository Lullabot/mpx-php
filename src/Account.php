<?php

namespace Mpx;

use GuzzleHttp\Client;

class Account {

  /**
   * @var string
   */
  public $username;

  /**
   * @var string
   */
  public $password;

  /**
   * @var Mpx/AuthenticationToken
   */
  private $token;

  public function __construct($username, $password, AuthenticationToken $token = NULL) {
    $this->username = $username;
    $this->password = $password;
    if (isset($token)) {
      $this->token = $token;
    }
  }

  public function getToken() {
    if (empty($this->token)) {
      $this->token = new AuthenticationToken($this);
    }
    if (!$this->token->isValid()) {
      $this->token->fetch();
    }
    return $this->token;
  }

}
