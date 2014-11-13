<?php

namespace Mpx;

use Psr\Log\LoggerInterface;

class Account {

  /**
   * @var \Psr\Log\LoggerInterface
   */
  private $logger;

  /**
   * @var string
   */
  private $username;

  /**
   * @var string
   */
  private $password;

  /**
   * @var \Mpx\AuthenticationToken
   */
  private $token;

  public function __construct(LoggerInterface $logger, $username, $password) {
    $this->logger = $logger;
    $this->username = $username;
    $this->password = $password;
  }

  public function setToken(AuthenticationToken $token) {
    $this->token = $token;
  }

  public function getUsername() {
    return $this->username;
  }

  public function getPassword() {
    return $this->password;
  }

  public function getToken($duration = NULL, $idleTimeout = NULL) {
    if (empty($this->token)) {
      $this->token = new AuthenticationToken($this->logger, $this);
    }
    if (!$this->token->isValid()) {
      $this->token->fetch($duration, $idleTimeout);
    }
    return $this->token;
  }

}
