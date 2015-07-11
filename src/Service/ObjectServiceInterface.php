<?php

namespace Mpx\Service;

use GuzzleHttp\Event\HasEmitterInterface;
use Mpx\HasCachePoolInterface;
use Mpx\HasClientInterface;
use Mpx\HasLoggerInterface;

interface ObjectServiceInterface extends HasCachePoolInterface, HasClientInterface, HasEmitterInterface, HasLoggerInterface {

  /**
   * @return string
   */
  public function getObjectType();

  /**
   * @return \Mpx\UserInterface
   */
  public function getUser();

  /**
   * @return \Psr\Http\Message\UriInterface
   */
  public function getUri();

  /**
   * Fetch an mpx object directly from the API.
   *
   * @param $id
   *   An array of IDs to fetch.
   * @param array $options
   *
   * @return \Mpx\Object\ObjectInterface
   *   An mpx objects.
   *
   * @throws \Mpx\Exception\ObjectNotFoundException
   */
  public function fetch($id, array $options = []);

  /**
   * Fetch multiple mpx objects directly from the API.
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
   * Fetch the total number of objects available from the API.
   *
   * @param array $options
   *
   * @return int
   *   The number of objects from the API.
   */
  public function getCount(array $options = []);

  /**
   * Load a single mpx object.
   *
   * @param $id
   *
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

  /**
   * Fetch an mpx object directly from the API by public ID.
   *
   * @param string $pid
   *
   * @return \Mpx\Object\ObjectInterface
   *
   * @throws \Mpx\Exception\ObjectNotFoundException
   */
  public function fetchByPid($pid);

  /**
   * Load a single mpx object by public ID.
   *
   * @param string $pid
   *
   * @return \Mpx\Object\ObjectInterface
   *
   * @throws \Mpx\Exception\ObjectNotFoundException
   */
  public function loadbyPid($pid);

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

  /**
   * @return \Mpx\Service\ObjectNotificationServiceInterface
   *
   * @throws \Mpx\Exception\NotificationsUnsupportedException
   */
  public function getNotificationService();

}
