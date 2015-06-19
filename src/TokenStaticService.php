<?php

namespace Mpx;

class TokenStaticService extends TokenServiceBase {

  /** @var array */
  static $tokens = array();

  /**
   * {@inheritdoc}
   */
  public static function load($username) {
    if (isset(static::$tokens[$username])) {
      return static::$tokens[$username];
    }
    else {
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(TokenInterface $token) {
    static::$tokens[$token->getUsername()] = $token;
  }

  /**
   * {@inheritdoc}
   */
  public function delete(TokenInterface $token) {
    unset(static::$tokens[$token->getUsername()]);
    parent::delete($token);
  }

  public function __destruct() {
    // Ensure all tokens are expired.
    foreach (static::$tokens as $token) {
      $this->delete($token);
    }
  }

}