<?php

namespace Mpx\Service;

use GuzzleHttp\Event\HasEmitterInterface;

interface ObjectServiceInterface extends HasEmitterInterface {

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
   * Fetch an mpx object from the API.
   *
   * @param $id
   *   An array of IDs to fetch.
   * @param array $options
   *
   * @return \Mpx\Object\ObjectInterface[]
   *   An array of mpx objects, indexed by ID.
   *
   * @throws \Mpx\Exception\ObjectNotFoundException
   */
  public function fetch($id, array $options = []);

  /**
   * Fetch multiple mpx objects from the API.
   *
   * @param array $ids
   *   An array of IDs to fetch.
   * @param array $options
   *
   * @return \Mpx\Object\ObjectInterface[]
   *   An array of mpx objects, indexed by ID.
   */
  public function fetchMultiple(array $ids, array $options = []);

  /**
   * @param $id
   * @return \Mpx\Object\ObjectInterface
   *
   * @throws \Mpx\Exception\ObjectNotFoundException
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
