<?php

namespace Mpx\Object;

use GuzzleHttp\Url;

class Player extends AbstractObject {

  /**
   * {@inheritdoc}
   */
  public static function getType() {
    return 'Player';
  }

  /**
   * {@inheritdoc}
   */
  public static function getSchema() {
    return '1.2';
  }

  /**
   * {@inheritdoc}
   */
  public static function getUri() {
    return Url::fromString('https://data.player.theplatform.com/player/data/Player');
  }

  /**
   * {@inheritdoc}
   */
  public static function getNotificationUri() {
    return Url::fromString('http://read.data.player.theplatform.com/player/notify?filter=Player');
  }

}
