<?php

namespace Mpx\Event;

use GuzzleHttp\Event\AbstractEvent;

class ObjectLoadEvent extends AbstractEvent {

  /** @var array */
  protected $objects;

  /**
   * @param array $objects
   */
  public function __construct(array $objects) {
    $this->objects = $objects;
  }

}
