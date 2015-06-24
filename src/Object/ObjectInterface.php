<?php

namespace Mpx\Object;

use Mpx\UserInterface;
use Pimple\Container;
use Mpx\Service\ObjectServiceInterface;

interface ObjectInterface {

  /**
   * @return string
   */
  public static function getType();

  /**
   * @return string
   */
  public static function getSchema();

  /**
   * @return \GuzzleHttp\Url|string
   *
   * @todo Could the schema and default form be merged into this URI?
   */
  public static function getUri();

  /**
   * Constructs a new mpx object, without saving it.
   *
   * @param array $values
   *   (optional) An array of values to set, keyed by property name.
   *
   * @return static
   */
  public static function create(array $values = array());

  /**
   * @return string
   */
  public function getId();

  /**
   * @return string
   */
  public function getTitle();

  /**
   * @return string
   */
  public function __toString();

  /**
   * @return \GuzzleHttp\Url
   */
  public static function getNotificationUri();

  /**
   * @param \Mpx\UserInterface $user
   * @param \Pimple\Container $container
   *
   * @return \Mpx\Service\ObjectServiceInterface
   */
  public static function createService(UserInterface $user, Container $container);

  /**
   * @return \Mpx\Service\NotificationServiceInterface
   *
   * @throws \Mpx\Exception\NotificationsUnsupportedException
   */
  public static function createNotificationService(ObjectServiceInterface $objectService, Container $container);

}
