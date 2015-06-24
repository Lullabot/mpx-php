<?php

namespace Mpx\Service;

interface ObjectServiceInterface {

  public function getSchema();

  public function getObjectType();

  public function getUser();

  public function getUri();

  /**
   * @param string $path
   * @param bool $readOnly
   * @return \GuzzleHttp\Url
   */
  public function generateUri($path = '', $readOnly = FALSE);

  public function load($id);

  /**
   * @return \Stash\Interfaces\PoolInterface
   */
  public function cache();
}
