<?php

/**
 * @file
 * Contains Mpx\ClientTrait.
 */

namespace Mpx;

trait ClientTrait {

  /**
   * @var \Mpx\ClientInterface
   */
  protected $client;

  /**
   * Sets a HTTP client.
   *
   * @param \Mpx\ClientInterface $client
   *
   * @return static
   */
  public function setClient(ClientInterface $client) {
    $this->client = $client;
    return $this;
  }

  /**
   * @return \Mpx\ClientInterface $client
   */
  public function client() {
    if (!isset($this->client)) {
      $this->client = new Client();
    }
    return $this->client;
  }

}
