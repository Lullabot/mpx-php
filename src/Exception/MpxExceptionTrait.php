<?php

namespace Lullabot\Mpx\Exception;

/**
 * Trait for MPX error data.
 *
 * This must be a trait instead of an abstract class as Guzzle has separate
 * inheritance trees for client and server exceptions.
 */
trait MpxExceptionTrait
{
    /**
     * @var array
     */
    protected $data;

    public function getTitle()
    {
        return $this->data['title'];
    }

    public function getDescription()
    {
        return $this->data['description'];
    }

    public function getCorrelationId()
    {
        return $this->data['correlationId'];
    }

    public function getServerStackTrace()
    {
        return $this->data['serverStackTrace'];
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        static::validateData($data);
        $this->data = $data;
    }

    /**
     * Validate required data in the MPX error.
     *
     * @param array $data The array of data returned by MPX.
     *
     * @throws \InvalidArgumentException Thrown if a required key in $data is missing.
     *
     * @see https://docs.theplatform.com/help/wsf-handling-data-service-exceptions#tp-toc4
     */
    public static function validateData($data)
    {
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
                throw new \InvalidArgumentException(sprintf('Required key %s is missing.', $key));
            }
        }
    }
}
