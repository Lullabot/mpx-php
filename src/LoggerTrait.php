<?php

namespace Mpx;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Basic Implementation of LoggerAwareInterface.
 */
trait LoggerTrait {

  /** @var \Psr\Log\LoggerInterface */
  protected $logger;

  /**
   * Sets a logger.
   *
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function setLogger(LoggerInterface $logger) {
    $this->logger = $logger;
  }

  /**
   * @return \Psr\Log\LoggerInterface $logger
   */
  public function logger() {
    if (!isset($this->logger)) {
      $this->logger = new NullLogger();
    }
    return $this->logger;
  }

}
