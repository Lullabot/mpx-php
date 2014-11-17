<?php

/**
 * @file
 * Contains Mpx\ClientTrait.
 */

namespace Mpx;

use GuzzleHttp\ClientInterface;

trait ClientTrait {

  /**
   * @var \GuzzleHttp\ClientInterface
   */
  protected $client;

  /**
   * Sets a HTTP client.
   *
   * @param \GuzzleHttp\ClientInterface $client
   */
  public function setClient(ClientInterface $client) {
    $this->client = $client;
  }
}
