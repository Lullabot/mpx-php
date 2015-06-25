<?php

namespace Mpx\Service;

use GuzzleHttp\Event\HasEmitterInterface;
use Mpx\HasClientInterface;

interface NotificationServiceInterface extends HasClientInterface, HasEmitterInterface {

  /**
   * @return \Mpx\UserInterface
   */
  public function getUser();

  /**
   * @return \GuzzleHttp\Url
   */
  public function getUri();

  /**
   * Get the last seen sequence ID for this notification service.
   *
   * @return string
   */
  public function getLastId();

  /**
   * Set the last seen sequence ID for the notification service.
   *
   * @param string $value
   *   The sequence ID to save.
   *
   * @return static
   */
  public function setLastId($value);

  /**
   * Retrieve the latest notification sequence ID for an account.
   *
   * @see http://help.theplatform.com/display/wsf2/Subscribing+to+change+notifications#Subscribingtochangenotifications-Synchronizingwiththenotificationssystem
   *
   * @param array $options
   *
   * @return string
   *   The notification sequence ID.
   *
   * @throws \Exception
   * @throws \UnexpectedValueException
   */
  public function fetchLatestId(array $options = []);

  /**
   * Perform a polling request for notifications.
   *
   * @see http://help.theplatform.com/display/wsf2/Subscribing+to+change+notifications#Subscribingtochangenotifications-Listeningfornotifications
   *
   * @param int $limit
   *   The number of notifications to request. If this value is greater than
   *   500, this will create multiple HTTP requests. If this value is NULL,
   *   this will run until no more notifications can possibly be retrieved.
   * @param array $options
   *
   * @return array
   *   An array of notifications.
   *
   * @throws \GuzzleHttp\Exception\RequestException
   * @throws \LogicException
   * @throws \Mpx\Exception\ApiException
   * @throws \Mpx\Exception\NotificationExpiredException
   */
  public function fetch($limit = 500, array $options = []);

  /**
   * Skip to the most recent notification sequence ID.
   *
   * @param array $options
   *   An additional array of options to pass to \Mpx\ClientInterface.
   */
  public function syncLatestId($options = []);

  /**
   * Read the most recent notifications.
   *
   * @param int $limit
   *   The maximum number of notifications to read.
   * @param array $options
   *   An additional array of options to pass to \Mpx\ClientInterface.
   *
   * @throws \Mpx\Exception\NotificationExpiredException
   */
  public function readNotifications($limit = 500, $options = []);

}
