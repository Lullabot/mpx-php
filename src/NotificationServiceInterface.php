<?php

namespace Mpx;

interface NotificationServiceInterface {

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
   */
  public function setLastId($value);

  /**
   * Retrieve the latest notification sequence ID for an account.
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
   * Perform a notification service request.
   *
   * @param bool $run_until_empty
   *   If TRUE will keep making requests to the notification URL until it
   *   returns no results.
   * @param array $options
   *
   * @return array()
   *   An array of notifications.
   *
   * @return \GuzzleHttp\Message\ResponseInterface
   * @throws \GuzzleHttp\Exception\RequestException
   * @throws \LogicException
   * @throws \Mpx\Exception\ApiException
   * @throws \Mpx\Exception\NotificationExpiredException
   */
  public function request($run_until_empty = FALSE, array $options = []);
}
