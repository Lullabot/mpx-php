<?php

namespace Mpx\Object;

class FeedConfig extends AbstractObject {

  /**
   * {@inheritdoc}
   */
  public static function getUri() {
    return 'http://data.feed.theplatform.com/feed/data/FeedConfig?schema=2.1&form=cjson';
  }

  /**
   * {@inheritdoc}
   */
  public static function getNotificationUri() {
    return 'http://read.data.feed.theplatform.com/feed/notify?filter=FeedConfig';
  }

}
