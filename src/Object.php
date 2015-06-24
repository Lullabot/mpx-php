<?php

namespace Mpx;

class Object {

  /** @var string */
  public $id;

  /** @var string */
  public $title;

  /** @var string */
  public $guid;

  /**
   * Constructs a new mpx object, without saving it.
   *
   * @param array $values
   *   (optional) An array of values to set, keyed by property name.
   *
   * @return static
   */
  public static function create(array $values = array()) {
    $instance = new static();
    foreach ($values as $key => $value) {
      $instance->{$key} = $value;
    }
    return $instance;
  }

  /**
   * @return string
   */
  public function __toString() {
    return $this->title . ' (id: ' . basename($this->id) . ')';
  }

}
