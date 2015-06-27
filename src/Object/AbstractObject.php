<?php

namespace Mpx\Object;

use Mpx\Service\ObjectService;
use Pimple\Container;
use Mpx\UserInterface;
use ReflectionClass;

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
  public static function getType() {
    $reflection = new ReflectionClass(get_called_class());
    return $reflection->getShortName();
  }

  /**
   * {@inheritdoc}
   */
  public function getId() {
    // Normalize the ID value to just the actual ID and not the full URL.
    return basename($this->id);
  }

  /**
   * {@inheritdoc}
   */
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
  public static function create(array $values = array()) {
    $instance = new static();
    foreach ($values as $key => $value) {
      $instance->{$key} = $value;
    }
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public static function createService(UserInterface $user, Container $container) {
    return ObjectService::create(get_called_class(), $user, $container);
  }

}
