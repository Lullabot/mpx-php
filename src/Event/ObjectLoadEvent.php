<?php

namespace Mpx\Event;

use GuzzleHttp\Event\AbstractEvent;
use Mpx\Service\ObjectServiceInterface;

class ObjectLoadEvent extends AbstractEvent {

  /** @var \Mpx\Service\ObjectServiceInterface */
  protected $objectService;

  /** @var \Mpx\Object\ObjectInterface[] */
  protected $objects;

  /**
   * @param \Mpx\Service\ObjectServiceInterface $objectService
   * @param \Mpx\Object\ObjectInterface[] $objects
   */
  public function __construct(ObjectServiceInterface $objectService, array $objects) {
    $this->objectService = $objectService;
    $this->objects = $objects;
  }

  /**
   * @return \Mpx\Service\ObjectServiceInterface
   */
  public function getObjectService() {
    return $this->objectService;
  }

  /**
   * @return \Mpx\Object\ObjectInterface[]
   */
  public function getObjects() {
    return $this->objects;
  }

}
