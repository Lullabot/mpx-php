<?php

/**
 * @file
 * Contains Mpx\TokenMemoryService.
 */

namespace Mpx;

class TokenMemoryService extends TokenServiceBase {

  /** @var array */
  protected static $tokens = array();

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
    $this->logger()->info(
      "Expiring {count} tokens in {method}.",
      array(
        'count' => count(static::$tokens),
        'method' => __METHOD__,
      )
    );

    // Since these tokens will not persist, ensure they are expired.
    foreach (static::$tokens as $token) {
      $this->delete($token);
    }
  }

}
