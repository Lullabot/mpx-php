<?php

namespace Mpx;

class Token implements TokenInterface {

  /**
   * The account username linked to the token.
   *
   * @var string
   */
  public $username;

  /**
   * The token string.
   *
   * @var string
   */
  public $value;

  /**
   * The UNIX timestamp of when the token expires.
   *
   * @var int
   */
  public $expire;

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
   * {@inheritdoc}
   */
  public function getUsername() {
    return $this->username;
  }

  /**
   * {@inheritdoc}
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getExpire() {
    return $this->expire;
  }

  /**
   * {@inheritdoc}
   */
  public function isValid($duration = NULL) {
    return $this->value && $this->expire > (time() + $duration);
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return $this->value;
  }

}