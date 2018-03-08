<?php

namespace Lullabot\Mpx\Exception;

interface MpxExceptionInterface {

    public function getTitle();

    public function getDescription();

    public function getCorrelationId();

    public function getServerStackTrace();

    public function getData();

    public function setData($data);

    public static function validateData($data);
}
