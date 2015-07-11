<?php

namespace Mpx\Event;

use GuzzleHttp\Event\AbstractEvent;
use Mpx\Service\NotificationServiceInterface;

class NotificationEvent extends AbstractEvent {

  /** @var \Mpx\Service\NotificationServiceInterface */
  protected $notificationService;

  /** @var array */
  protected $notifications;

  /**
   * @param \Mpx\Service\NotificationServiceInterface $notificationService
   * @param array $notifications
   */
  public function __construct(NotificationServiceInterface $notificationService, array &$notifications) {
    $this->notificationService = $notificationService;
    $this->notifications = &$notifications;
  }

  /**
   * @return array
   */
  public function getNotifications() {
    return $this->notifications;
  }

  /**
   * @return \Mpx\Service\NotificationServiceInterface
   */
  public function getNotificationService() {
    return $this->notificationService;
  }

}
