<?php

namespace Mpx\Object;

use GuzzleHttp\Psr7\Uri;

class Player extends AbstractObject {

  /**
   * {@inheritdoc}
   */
  public static function getUri() {
    return new Uri('https://data.player.theplatform.com/player/data/Player?schema=1.2&form=cjson');
  }

  /**
   * {@inheritdoc}
   */
  public static function getReadOnlyUri() {
    return new Uri('https://read.data.player.theplatform.com/player/data/Player?schema=1.2&form=cjson');
  }

  /**
   * {@inheritdoc}
   */
  public static function getNotificationUri() {
    return new Uri('https://read.data.player.theplatform.com/player/notify?filter=Player');
  }

}
