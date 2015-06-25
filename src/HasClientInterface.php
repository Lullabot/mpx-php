<?php

namespace Mpx;

/**
 * Holds an mpx client.
 */
interface HasClientInterface {

  /**
   * Get the mpx client of the object.
   *
   * @return \Mpx\ClientInterface
   */
  public function getClient();

}
