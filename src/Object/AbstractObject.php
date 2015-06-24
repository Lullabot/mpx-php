<?php

namespace Mpx\Object;

use Mpx\Service\ObjectNotificationService;
use Mpx\Service\ObjectService;
use Pimple\Container;
use Mpx\UserInterface;
use Mpx\Service\ObjectServiceInterface;
use Mpx\Exception\NotificationsUnsupportedException;

abstract class AbstractObject implements ObjectInterface {

  /** @var string */
  public $id;

  /** @var string */
  public $title;

  /** @var string */
  public $guid;

  /**
   * {@inheritdoc}
   */
  public static function create(array $values = array()) {
    $instance = new static();
    foreach ($values as $key => $value) {
      $instance->{$key} = $value;
    }
    return $instance;
  }

  public function getId() {
    // Normalize the ID value to just the actual ID and not the full URL.
    return basename($this->id);
  }

  public function getTitle() {
    return $this->title;
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return $this->getTitle() . ' (id: ' . $this->getId() . ')';
  }

  /**
   * {@inheritdoc}
   */
  public static function createService(UserInterface $user, Container $container) {
    return ObjectService::create(get_called_class(), $user, $container);
  }

  /**
   * {@inheritdoc}
   */
  public static function createNotificationService(ObjectServiceInterface $objectService, Container $container) {
    $uri = static::getNotificationUri();
    if (!$uri) {
      throw new NotificationsUnsupportedException("The " . static::getType() . " object does not support notifications.");
    }

    // Allow some query parameters to be reused from the object service's URI.
    $uri->getQuery()->merge($objectService->getUri()->getQuery()->filter(function($key, $value) {
      return in_array($key, array('account'));
    }));

    return ObjectNotificationService::create($uri, $objectService, $container);
  }

}
