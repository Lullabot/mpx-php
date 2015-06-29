<?php

namespace Mpx\Subscriber;

use GuzzleHttp\Event\SubscriberInterface;
use Mpx\HasLoggerInterface;
use Mpx\HasLoggerTrait;
use Mpx\Event\NotificationEvent;
use Mpx\Service\ObjectNotificationServiceInterface;
use Psr\Log\LoggerInterface;

class NotificationLogSubscriber implements SubscriberInterface, HasLoggerInterface {
  use HasLoggerTrait;

  /**
   * @param LoggerInterface|null $logger
   */
  public function __construct(LoggerInterface $logger = null) {
    $this->logger = $logger;
  }

  public function getEvents() {
    return [
      'notify' => ['onNotify'],
    ];
  }

  public function onNotify(NotificationEvent $event) {
    $objects = array();
    if ($event->getNotificationService() instanceof ObjectNotificationServiceInterface) {
      /** @var \Mpx\Service\ObjectNotificationServiceInterface $notificationService */
      $notificationService = $event->getNotificationService();
      $objectService = $notificationService->getObjectService();
      if ($ids = $notificationService::extractIdsFromNotifications($event->getNotifications())) {
        $objects = $objectService->loadMultiple($ids);
      }
    }
    foreach ($event->getNotifications() as $notification_id => $notification) {
      if (!empty($notification['id'])) {
        $this->getLogger()->info(
          "NOTIFICATION {notification-id}\t{method}\t{type}\t{id}",
          array(
            'notification-id' => $notification_id,
            'method' => $notification['method'],
            'type' => $notification['type'],
            'id' => isset($objects[$notification['id']]) ? (string) $objects[$notification['id']] : $notification['id'],
          )
        );
      }
    }
  }

}
