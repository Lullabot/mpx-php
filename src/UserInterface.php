<?php

/**
 * @file
 * Contains Mpx\UserInterface.
 */

namespace Mpx;

/**
 * Defines an interface for interacting with MPX users.
 *
 * @see http://help.theplatform.com/display/wsf2/Identity+management+service+API+reference
 * @see http://help.theplatform.com/display/wsf2/User+operations
 */
interface UserInterface {

  /**
   * Get the username of the mpx user.
   *
   * @return string
   */
  public function getUsername();

  /**
   * Get the password of the mpx user.
   *
   * @return string
   */
  public function getPassword();

  /**
   * Get a current authentication token for the account.
   *
   * @param int $duration
   *   The number of seconds for which the token should be valid.
   * @param bool $force
   *   Set to TRUE if a fresh authentication token should always be fetched.
   *
   * @return string
   *   A valid MPX authentication token.
   */
  public function acquireToken($duration = NULL, $force = FALSE);

  /**
   * Invalidate the current authentication token for the account.
   */
  public function invalidateToken();

  /**
   * Sign in the user and return the current token.
   */
  public function signIn();

  /**
   * Sign out the user.
   */
  public function signOut();

  public function getSelfId();

  public function getId();

}
