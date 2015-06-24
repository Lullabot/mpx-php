<?php

namespace Mpx\Service;

interface ObjectServiceInterface {

  public function getSchema();

  public function getObjectType();

  /**
   * @return \Mpx\UserInterface
   */
  public function getUser();

  public function getUri();

  /**
   * @param string $path
   * @return \GuzzleHttp\Url
   */
  public function generateUri($path = '');

  public function request($method = 'GET', $path, array $options = []);

  public function load($id);

  /**
   * Load multiple mpx objects.
   *
   * @param array $ids
   *   An array of IDs to load.
   *
   * @return array
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

  public function callObjectClass();

  /**
   * @return \GuzzleHttp\Url
   */
  public function getNotificationUri();
}
