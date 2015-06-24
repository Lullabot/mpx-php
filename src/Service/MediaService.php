<?php

namespace Mpx\Service;

use GuzzleHttp\Url;
use Mpx\ClientInterface;
use Mpx\UserInterface;
use Pimple\Container;
use Psr\Log\LoggerInterface;
use Stash\Interfaces\PoolInterface;

class MediaService extends AbstractObjectService {

  /**
   * Construct an mpx media object service.
   *
   * @param \Mpx\UserInterface $user
   * @param \Mpx\ClientInterface $client
   * @param \Stash\Interfaces\PoolInterface $cache
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function __construct(UserInterface $user, ClientInterface $client = NULL, PoolInterface $cache = NULL, LoggerInterface $logger = NULL) {    parent::__construct($user, $client, $cache, $logger);
    parent::__construct($user, $client, $cache, $logger);
    $this->uri = Url::fromString('https://data.media.theplatform.com/media');
  }

  /**
   * {@inheritdoc}
   */
  public function getObjectType() {
    return 'Media';
  }

  /**
   * {@inheritdoc}
   */
  public function getSchema() {
    return '1.6';
  }
}
