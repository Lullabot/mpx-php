<?php

/**
 * @file
 * Contains Mpx\ClientInterface.
 */

namespace Mpx;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;

interface ClientInterface extends GuzzleClientInterface {

  /**
   * @param UserInterface $user
   * @param string|\GuzzleHttp\Url|null $url
   * @param array $options
   *
   * @return \GuzzleHttp\Message\ResponseInterface
   * @throws \GuzzleHttp\Exception\RequestException
   * @throws \Mpx\Exception\ApiException
   */
  public function authenticatedGet(UserInterface $user, $url = null, $options = []);

}
