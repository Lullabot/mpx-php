<?php

namespace Mpx\Object;

class Player extends AbstractObject {

  /**
   * {@inheritdoc}
   */
  public static function getUri() {
    return 'https://data.player.theplatform.com/player/data/Player?schema=1.2&form=cjson';
  }

  /**
   * {@inheritdoc}
   */
  public static function getNotificationUri() {
    return 'http://read.data.player.theplatform.com/player/notify?filter=Player';
  }

}
