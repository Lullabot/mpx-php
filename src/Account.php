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

  /**
   * @param \Mpx\AuthenticationToken $token
   *
   * @return $this
   */
  public function setToken(AuthenticationToken $token) {
    $this->token = $token;
    return $this;
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
   * @return \Psr\Log\LoggerInterface
   */
  public function getLogger() {
    return $this->logger;
  }

  /**
   * Gets a current authentication token for the account.
   *
   * @param int $duration
   *   The duration of the token, in milliseconds.
   * @param int $idleTimeout
   *   The idle timeout for the token, in milliseconds.
   *
   * @return \Mpx\AuthenticationToken
   *
   * @throws \Exception
   */
  public function getToken($duration = NULL, $idleTimeout = NULL) {
    if (empty($this->token)) {
      $this->token = new AuthenticationToken($this);
      $this->token->fetch($duration, $idleTimeout);
    }
    if (!$this->token->isValid()) {
      $this->logger->info("Expired or invalid MPX token {$this->token} for {$this->username}. Fetching new token.");
      $this->token->fetch($duration, $idleTimeout);
    }
    return $this->token;
  }

}
