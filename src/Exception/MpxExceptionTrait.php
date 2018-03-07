<?php

namespace Lullabot\Mpx\Exception;

trait MpxExceptionTrait {

    /**
     * @var array
     */
    protected $data;

    public function getTitle() {
        return $this->data['title'];
    }

    public function getDescription() {
        return $this->data['description'];
    }

    public function getCorrelationId() {
        return $this->data['correlationId'];
    }

    public function getServerStackTrace() {
        return $this->data['serverStackTrace'];
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        static::validateData($data);
        $this->data = $data;
    }

    public static function validateData($data) {
        // @todo Prior code also checked for $data being an array, but the docs
        // at https://docs.theplatform.com/help/wsf-handling-data-service-exceptions#tp-toc4
        // don't show that.
        $required = [
            'responseCode',
            'isException',
            'title',
            'description',
        ];
        foreach ($required as $key) {
            if (empty($data[$key])) {
                throw new \InvalidArgumentException(sprintf("Required key %s is missing.", $key));
            }
        }
    }
}
