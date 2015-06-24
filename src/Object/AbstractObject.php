<?php

namespace Mpx\Object;

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
  public static function getNotificationUri() {
    $uri = static::getUri();
    $uri->setHost('read.' . $uri->getHost());
    $uri->setPath(str_replace('/data/' . static::getType(), '', $uri->getPath()) . '/notify');
    // Ensure we only filter to objects of this type.
    $uri->getQuery()->set('filter', static::getType());
    return $uri;
  }

}
