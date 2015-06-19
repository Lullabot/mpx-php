<?php

/**
 * @file
 * Contains Mpx\TokenServiceTrait.
 */

namespace Mpx;

use Psr\Log\LoggerInterface;

trait TokenServiceTrait {

  /**
   * @var \Mpx\TokenServiceInterface
   */
  protected $tokenService;

  /**
   * Sets the mpx token service.
   *
   * @param \Mpx\TokenServiceInterface $tokenService
   *
   * @return static
   */
  public function setTokenService(TokenServiceInterface $tokenService) {
    $this->tokenService = $tokenService;
    return $this;
  }

  /**
   * Returns the current mpx token service.
   *
   * @return \Mpx\TokenServiceInterface $client
   */
  public function tokenService() {
    if (!isset($this->tokenService)) {
      $this->tokenService = new TokenMemoryService();
      if (isset($this->client) && $this->client instanceof ClientInterface) {
        $this->tokenService->setClient($this->client);
      }
      if (isset($this->logger) && $this->logger instanceof LoggerInterface) {
        $this->tokenService->setLogger($this->logger);
      }
    }
    return $this->tokenService;
  }

}
