<?php

namespace Mpx\Object;

use GuzzleHttp\Psr7\Uri;

class FeedConfig extends AbstractObject {

  /**
   * {@inheritdoc}
   */
  public static function getUri() {
    return new Uri('http://data.feed.theplatform.com/feed/data/FeedConfig?schema=2.1&form=cjson');
  }

  /**
   * {@inheritdoc}
   */
  public static function getReadOnlyUri() {
    return new Uri('http://read.data.feed.theplatform.com/feed/data/FeedConfig?schema=2.1&form=cjson');
  }

  /**
   * {@inheritdoc}
   */
  public static function getNotificationUri() {
    return new Uri('http://read.data.feed.theplatform.com/feed/notify?filter=FeedConfig');
  }

}
