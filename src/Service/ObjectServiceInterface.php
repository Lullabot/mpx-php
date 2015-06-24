<?php

namespace Mpx\Service;

use Pimple\Container;

interface ObjectServiceInterface {

  /**
   * @return string
   */
  public function getObjectType();

  /**
   * @return \Mpx\UserInterface
   */
  public function getUser();

  /**
   * @return \GuzzleHttp\Url
   */
  public function getUri();

  /**
   * @param $id
   * @return \Mpx\Object\ObjectInterface
   */
  public function load($id);

  /**
   * Load multiple mpx objects.
   *
   * @param array $ids
   *   An array of IDs to load.
   *
   * @return \Mpx\Object\ObjectInterface[]
   *   An array of mpx objects, indexed by ID.
   *
   * @throws \Exception
   */
  public function loadMultiple(array $ids);

  public function resetCache(array $ids = NULL);

  /**
   * @param array $data
   *
   * @return \Mpx\Object\ObjectInterface
   */
  public function createObject(array $data);

  /**
   * @param array $data
   * @return \Mpx\Object\ObjectInterface[]
   */
  public function createObjects(array $data);
}
