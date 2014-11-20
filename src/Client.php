<?php

/**
 * @file
 * Contains Mpx\Client.
 */

namespace Mpx;

use GuzzleHttp\Client as GuzzleClient;

class Client extends GuzzleClient {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $config = []) {
    // @todo Figure out what query strings we can actually hard-code here.
    $config['defaults']['query']['schema'] = '1.0';
    $config['defaults']['query']['httpError'] = 'true';
    $config['defaults']['query']['form'] = 'json';
    parent::__construct($config);
  }
}
