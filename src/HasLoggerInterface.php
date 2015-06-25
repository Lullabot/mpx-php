<?php

namespace Mpx;

use Psr\Log\LoggerInterface;

/**
 * Holds an logger.
 */
interface HasLoggerInterface {

  /**
   * Sets a logger for the object.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *
   * @return static
   */
  public function setLogger(LoggerInterface $logger);

  /**
   * Get the logger of the object.
   *
   * @return \Psr\Log\LoggerInterface
   */
  public function getLogger();

}
