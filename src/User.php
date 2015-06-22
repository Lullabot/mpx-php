<?php

/**
 * @file
 * Contains Mpx\User.
 */

namespace Mpx;

use Pimple\Container;
use Psr\Log\LoggerInterface;

class User implements UserInterface {
  use ClientTrait;
  use LoggerTrait;
  use TokenServiceTrait;

  /**
   * @var string
   */
  private $username;

  /**
   * @var string
   */
  private $password;

  /**
   * @param string $username
   * @param string $password
   * @param \Mpx\ClientInterface $client
   * @param \Psr\Log\LoggerInterface $logger
   * @param \Mpx\TokenServiceInterface $tokenService
   */
  public function __construct($username, $password, ClientInterface $client = NULL, LoggerInterface $logger = NULL, TokenServiceInterface $tokenService = NULL) {
    $this->username = $username;
    $this->password = $password;
    $this->client = $client;
    $this->logger = $logger;
    $this->tokenService = $tokenService;
  }

  /**
   * @param string $username
   * @param string $password
   * @param \Pimple\Container $container
   *
   * @return static
   */
  public static function create($username, $password, Container $container) {
    return new static(
      $username,
      $password,
      $container['client'],
      $container['logger'],
      $container['token.service']
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
  public function acquireToken($duration = NULL, $force = FALSE) {
    $token = $this->tokenService()->load($this->username);

    if ($force || !$token || !$token->isValid($duration)) {
      // Delete the token from the cache first in case there is a failure in
      // MpxToken::fetch() below.
      if ($token) {
        $this->tokenService()->delete($token);
      }

      // @todo Validate if the new token also valid for $duration.
      $token = $this->tokenService()->fetch($this->username, $this->password);
      $this->tokenService()->save($token);
    }

    return $token;
  }

  /**
   * {@inheritdoc}
   */
  public function releaseToken() {
    if ($token = $this->tokenService()->load($this->username)) {
      $this->tokenService()->delete($token);
    }
  }

}
