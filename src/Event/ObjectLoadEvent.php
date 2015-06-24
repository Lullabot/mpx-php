<?php

namespace Mpx\Event;

use GuzzleHttp\Event\AbstractEvent;

class ObjectLoadEvent extends AbstractEvent {

  /** @var \Mpx\Object\ObjectInterface[] */
  protected $objects;

  /**
   * @param \Mpx\Object\ObjectInterface[] $objects
   */
  public function __construct(array $objects) {
    $this->objects = $objects;
  }

  /**
   * @return \Mpx\Object\ObjectInterface[]
   */
  public function getObjects() {
    return $this->objects;
  }

}
