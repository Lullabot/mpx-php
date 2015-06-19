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
  }

}