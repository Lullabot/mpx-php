<?php

namespace Mpx;

interface TokenInterface {

  /**
   * Maximum possible token TTL is one week, expressed in seconds.
   *
   * @todo Should this value be lower?
   *
   * @var int
   */
  const MAX_TTL = 604800;

  /**
   * @return string
   */
  public function getUsername();

  /**
   * @return string
   */
  public function getValue();

  /**
   * @return int
   */
  public function getExpire();

  /**
   * Checks if a token is valid.
   *
   * @param int $duration
   *   The number of seconds for which the token should be valid. Otherwise
   *   this will just check if the token is still valid for the current time.
   *
   * @return bool
   *   TRUE if the token is valid, or FALSE otherwise.
   */
  public function isValid($duration = NULL);

  /**
   * Invalidate the token by setting $expire to 0.
   */
  public function invalidate();

  /**
   * @return string
   */
  public function __toString();

}
