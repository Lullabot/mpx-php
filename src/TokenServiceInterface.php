<?php

/**
 * @file
 * Contains Mpx\TokenServiceInterface.
 */

namespace Mpx;

interface TokenServiceInterface {

  /**
   * Load a token.
   *
   * In most cases, using \Mpx\User::acquireToken() is recommended since this
   * may return an expired token object.
   *
   * @param string $username
   *   The mpx account username.
   *
   * @return \Mpx\Token
   *   The token object if available.
   */
  public function load($username);

  /**
   * Load all saved tokens.
   *
   * @return \Mpx\Token[]
   */
  public function loadAll();

  /**
   * Save a token.
   *
   * @param \Mpx\Token $token
   */
  public function save(Token $token);

  /**
   * Delete a saved token.
   *
   * @param \Mpx\Token $token
   */
  public function delete(Token $token);

  /**
   * Fetch a fresh authentication token using thePlatform API.
   *
   * In most cases, using \Mpx\User::acquireToken() is recommended.
   *
   * @param string $username
   *   The mpx account username.
   * @param string $password
   *   The mpx account password.
   * @param int $duration
   *   The number of seconds for which the token should be valid.
   *
   * @return \Mpx\Token
   *   The token object if a token was fetched.
   *
   * @throws \Mpx\Exception\ApiException
   */
  public function fetch($username, $password, $duration = NULL);

  /**
   * Expire an authentication token using thePlatform API.
   *
   * In most cases, using \Mpx\User::releaseToken() is recommended instead.
   * This method only interacts with the thePlatform API and does not delete
   * the token from the cache.
   *
   * @param \Mpx\Token $token
   *
   * @throws \Mpx\Exception\ApiException
   */
  public function expire(Token $token);

}
