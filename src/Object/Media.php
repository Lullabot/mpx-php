<?php

namespace Mpx\Object;

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
    return 'http://data.media.theplatform.com/media/data/Media?schema=1.7.0&form=cjson';
  }

  /**
   * {@inheritdoc}
   */
  public static function getNotificationUri() {
    return 'http://read.data.media.theplatform.com/media/notify?filter=Media';
  }

}
