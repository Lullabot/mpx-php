<?php

namespace Mpx\Service;

use GuzzleHttp\Url;
use Mpx\ClientInterface;
use Mpx\UserInterface;
use Pimple\Container;
use Psr\Log\LoggerInterface;
use Stash\Interfaces\PoolInterface;

class PlayerService extends AbstractObjectService {

  /**
   * Construct an mpx player object service.
   *
   * @param \Mpx\UserInterface $user
   * @param \Mpx\ClientInterface $client
   * @param \Stash\Interfaces\PoolInterface $cache
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function __construct(UserInterface $user, ClientInterface $client = NULL, PoolInterface $cache = NULL, LoggerInterface $logger = NULL) {
    parent::__construct($user, $client, $cache, $logger);
    $this->uri = Url::fromString('https://data.player.theplatform.com/player');
  }

  /**
   * {@inheritdoc}
   */
  public function getObjectType() {
    return 'Player';
  }

  /**
   * {@inheritdoc}
   */
  public function getSchema() {
    return '1.2';
  }

}
