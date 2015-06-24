<?php

namespace Mpx\Service;

interface ObjectNotificationServiceInterface extends NotificationServiceInterface {

  /**
   * @return \Mpx\Service\ObjectServiceInterface
   */
  public function getObjectService();

  /**
   * @param array $notifications
   * @return array
   */
  public static function extractIdsFromNotifications(array $notifications);

}
