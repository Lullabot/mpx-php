<?php

/**
 * @file
 * Contains Mpx\ClientTrait.
 */

namespace Mpx;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client;

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

  /**
   * @return \GuzzleHttp\ClientInterface $client
   */
  public function getClient() {
    $this->setClient(new Client());
    return $this->client;
  }
}
