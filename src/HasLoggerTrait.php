<?php

/**
 * @file
 * Contains Mpx\LoggerTrait.
 */

namespace Mpx;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

trait HasLoggerTrait {

  /** @var \Psr\Log\LoggerInterface */
  protected $logger;

  /**
   * Sets a logger.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *
   * @return static
   */
  public function setLogger(LoggerInterface $logger) {
    $this->logger = $logger;
    return $this;
  }

  /**
   * @return \Psr\Log\LoggerInterface $logger
   */
  public function getLogger() {
    if (!isset($this->logger)) {
      $this->logger = new NullLogger();
    }
    return $this->logger;
  }

}
