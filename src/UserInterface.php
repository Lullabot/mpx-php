<?php

/**
 * @file
 * Contains Mpx\UserInterface.
 */

namespace Mpx;

use GuzzleHttp\ClientInterface;
use Pimple\Container;
use Psr\Log\LoggerInterface;

/**
 * Defines an interface for interacting with MPX users.
 *
 * @see http://help.theplatform.com/display/wsf2/Identity+management+service+API+reference
 * @see http://help.theplatform.com/display/wsf2/User+operations
 */
interface UserInterface {

  /**
   * @param string $username
   * @param string $password
   * @param \GuzzleHttp\ClientInterface $client
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function __construct($username, $password, ClientInterface $client, LoggerInterface $logger);

  /**
   * @param string $username
   * @param string $password
   * @param \Pimple\Container $container
   * @return \Mpx\UserInterface
   */
  public static function create($username, $password, Container $container);

  /**
   * @return string
   */
  public function getUsername();

  /**
   * @return string
   */
  public function getPassword();

  /**
   * Set the current authentication token for the account
   *
   * @param string $token
   *   The token string.
   * @param int $expires
   *   A UNIX timestamp of when the token is set to expire.
   *
   * @throws \Mpx\Exception\InvalidTokenException
   */
  public function setToken($token, $expires);

  /**
   * Gets a current authentication token for the account.
   *
   * @return string
   */
  public function getToken();

  /**
   * Gets a valid authentication token for the account.
   *
   * If the current token from getToken() is not valid,
   * a new token will be fetched using signIn().
   *
   * @return string
   */
  public function getValidToken();

  public function setExpires($expires);

  /**
   * @return int
   */
  public function getExpires();

  /**
   * Checks if the user's current token is valid.
   *
   * @return bool
   *   TRUE if the current token is valid, or FALSE if the token is not valid.
   */
  public function hasValidToken();

  /**
   * Checks if a token is valid.
   *
   * @param string $token
   *   The token string.
   * @param int $expires
   *   A UNIX timestamp of when the token is set to expire.
   *
   * @return bool
   *   TRUE if the token is valid, or FALSE if the token is not valid.
   */
  public static function isValidToken($token, $expires);

  /**
   * Signs in the user.
   *
   * @param int $duration
   *   The duration of the token, in milliseconds.
   * @param int $idleTimeout
   *   The idle timeout for the token, in milliseconds.
   *
   * @return $this
   *
   * @throws \GuzzleHttp\Exception\RequestException
   * @throws \RuntimeException
   * @throws \Mpx\Exception\InvalidTokenException
   *
   * @see http://help.theplatform.com/display/wsf2/signIn+method
   */
  public function signIn($duration = NULL, $idleTimeout = NULL);

  /**
   * Signs out the user.
   *
   * @throws \GuzzleHttp\Exception\RequestException
   *
   * @see http://help.theplatform.com/display/wsf2/signOut+method
   */
  public function signOut();

}
