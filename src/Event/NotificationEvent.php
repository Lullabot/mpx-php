<?php

namespace Mpx\Event;

use GuzzleHttp\Event\AbstractEvent;

class NotificationEvent extends AbstractEvent {

  /** @var array */
  protected $notifications;

  /**
   * @param array $notifications
   */
  public function __construct(array &$notifications) {
    $this->notifications = &$notifications;
  }

  /**
   * @return array
   */
  public function getNotifications() {
    return $this->notifications;
  }

  public function setNotifications(array $notifications) {
    $this->notifications = $notifications;
  }

}
