<?php

namespace Mpx\Object;

use GuzzleHttp\Url;

class Media extends AbstractObject {

  /**
   * {@inheritdoc}
   */
  public static function getType() {
    return 'Media';
  }

  /**
   * {@inheritdoc}
   */
  public static function getUri() {
    return Url::fromString('http://data.media.theplatform.com/media/data/Media?schema=1.6&form=cjson');
  }

  /**
   * {@inheritdoc}
   */
  public static function getNotificationUri() {
    return Url::fromString('http://read.data.media.theplatform.com/media/notify?filter=Media');
  }

}
