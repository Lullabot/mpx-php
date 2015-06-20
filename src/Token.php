<?php

/**
 * @file
 * Contains Mpx\Token.
 */

namespace Mpx;

class Token {

  /**
   * Maximum possible token TTL is one week, expressed in seconds.
   *
   * @todo Should this value be lower?
   *
   * @var int
   */
  const MAX_TTL = 604800;

  /**
   * The account username linked to the token.
   *
   * @var string
   */
  protected $username;

  /**
   * The token string.
   *
   * @var string
   */
  protected $value;

  /**
   * The UNIX timestamp of when the token expires.
   *
   * @var int
   */
  protected $expire;

  /**
   * Construct an MPX token object.
   *
   * @param string $username
   *   The account username linked to the token.
   * @param string $value
   *   The token string.
   * @param int $expire
   *   The UNIX timestamp of when the token expires.
   */
  public function __construct($username, $value, $expire) {
    $this->username = $username;
    $this->value = $value;
    $this->expire = $expire;
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
  public function getValue() {
    return $this->value;
  }

  /**
   * @return int
   */
  public function getExpire() {
    return $this->expire;
  }

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
  public function isValid($duration = NULL) {
    return $this->value && $this->expire > (time() + $duration);
  }

  /**
   * Invalidate the token by setting $expire to 0.
   */
  public function invalidate() {
    $this->expire = 0;
  }

  /**
   * @return string
   */
  public function __toString() {
    return $this->value;
  }

}
