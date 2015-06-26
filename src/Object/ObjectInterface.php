<?php

namespace Mpx\Object;

use Mpx\UserInterface;
use Pimple\Container;

interface ObjectInterface {

  /**
   * @return string
   */
  public static function getType();

  /**
   * @return \GuzzleHttp\Url|string
   */
  public static function getUri();

  /**
   * @return \GuzzleHttp\Url|string
   */
  public static function getNotificationUri();

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
   * Constructs a new mpx object, without saving it.
   *
   * @param array $values
   *   (optional) An array of values to set, keyed by property name.
   *
   * @return static
   */
  public static function create(array $values = array());

  /**
   * @param \Mpx\UserInterface $user
   * @param \Pimple\Container $container
   *
   * @return \Mpx\Service\ObjectServiceInterface
   */
  public static function createService(UserInterface $user, Container $container);

}
