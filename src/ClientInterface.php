<?php

/**
 * @file
 * Contains Mpx\ClientInterface.
 */

namespace Mpx;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;

interface ClientInterface extends GuzzleClientInterface {

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
