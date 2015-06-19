<?php

namespace Mpx;

class TokenMemoryService extends TokenServiceBase {

  /** @var array */
  static $tokens = array();

  /**
   * {@inheritdoc}
   */
  public function load($username) {
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
  public function loadAll() {
    return static::$tokens;
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
    // Since these tokens will not persist, ensure they are expired.
    foreach (static::$tokens as $token) {
      $this->delete($token);
    }
  }

}
