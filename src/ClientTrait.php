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
   */
  public function setClient(ClientInterface $client) {
    $this->client = $client;
  }

  /**
   * @return \Mpx\ClientInterface $client
   */
  public function client() {
    if (!isset($this->client)) {
      $this->setClient(new Client());
    }
    return $this->client;
  }

}
