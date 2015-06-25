<?php

namespace Mpx;

/**
 * Holds an mpx HTTP client.
 */
interface HasClientInterface {

  /**
   * Sets the mpx HTTP client for the object.
   *
   * @param \Mpx\ClientInterface $client
   *
   * @return static
   */
  public function setClient(ClientInterface $client);

  /**
   * Get the mpx HTTP client of the object.
   *
   * @return \Mpx\ClientInterface
   */
  public function getClient();

}
