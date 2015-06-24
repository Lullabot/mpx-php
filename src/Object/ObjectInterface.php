<?php

namespace Mpx\Object;

interface ObjectInterface {

  /**
   * @return string
   */
  public static function getType();

  /**
   * @return string
   */
  public static function getSchema();

  /**
   * @return \GuzzleHttp\Url|string
   *
   * @todo Could the schema and default form be merged into this URI?
   */
  public static function getUri();

  /**
   * Constructs a new mpx object, without saving it.
   *
   * @param array $values
   *   (optional) An array of values to set, keyed by property name.
   *
   * @return static
   */
  public static function create(array $values = array());

  /**
   * @return string
   */
  public function getId();

  /**
   * @return string
   */
  public function getTitle();

  /**
   * @return string
   */
  public function __toString();
}
