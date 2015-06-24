<?php

/**
 * @file
 * Contains Mpx\ClientInterface.
 */

namespace Mpx;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;

interface ClientInterface extends GuzzleClientInterface {

  public function authenticatedRequest($method = 'GET', $url = null, UserInterface $user, $options = []);

  /**
   * Perform an authenticated HTTP GET request on behalf of $user.
   *
   * @param UserInterface $user
   * @param string|\GuzzleHttp\Url|null $url
   * @param array $options
   *
   * @return array
   * @throws \GuzzleHttp\Exception\RequestException
   * @throws \Mpx\Exception\ApiException
   */
  public function authenticatedGet(UserInterface $user, $url = null, $options = []);

}
