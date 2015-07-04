<?php

namespace Mpx\Object;

use GuzzleHttp\Psr7\Uri;

class Media extends AbstractObject {

  /**
   * {@inheritdoc}
   */
  public static function getUri() {
    return new Uri('http://data.media.theplatform.com/media/data/Media?schema=1.7&form=cjson');
  }

  /**
   * {@inheritdoc}
   */
  public static function getReadOnlyUri() {
    return new Uri('https://read.data.media.theplatform.com/media/data/Media?schema=1.7&form=cjson');
  }

  /**
   * {@inheritdoc}
   */
  public static function getNotificationUri() {
    return new Uri('http://read.data.media.theplatform.com/media/notify?filter=Media');
  }

}
