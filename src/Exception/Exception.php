<?php

/**
 * @file
 * Contains Mpx\Exception.
 */

namespace Mpx\Exception;

/**
 * An exception class to be thrown on generic MPX errors.
 */
class Exception extends \Exception {

  public function setMessage($message) {
    $this->message = $message;
  }

}
