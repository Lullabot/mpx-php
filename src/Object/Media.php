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
  public static function getSchema() {
    return '1.6';
  }

  /**
   * {@inheritdoc}
   */
  public static function getUri() {
    return Url::fromString('http://data.media.theplatform.com/media/data/Media');
  }

}
