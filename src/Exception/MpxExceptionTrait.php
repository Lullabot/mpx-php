<?php

namespace Lullabot\Mpx\Exception;

use Psr\Http\Message\ResponseInterface;

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

    /**
     * Return the error title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->data['title'];
    }

    /**
     * Return the error description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->data['description'];
    }

    /**
     * Return the request correlation ID.
     *
     * @throws \OutOfBoundsException Thrown when correlation ID is not set.
     *
     * @return string
     */
    public function getCorrelationId(): string
    {
        if (!isset($this->data['correlationId'])) {
            throw new \OutOfBoundsException('correlationId is not included in this error.');
        }

        return $this->data['correlationId'];
    }

    /**
     * Return the server stack trace.
     *
     * @throws \OutOfBoundsException Thrown when the stack trace is not set.
     *
     * @return string
     */
    public function getServerStackTrace(): string
    {
        if (!isset($this->data['serverStackTrace'])) {
            throw new \OutOfBoundsException('serverStackTrace is not included in this error.');
        }

        return $this->data['serverStackTrace'];
    }

    /**
     * Return all data associated with this error.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Validate and set the data for this error.
     *
     * @param array $data The MPX error data.
     */
    public function setData(array $data)
    {
        static::validateData($data);
        $this->data = $data;
    }

    /**
     * Set data from a notification exception.
     *
     * @param array $data The notification exception data.
     */
    public function setNotificationData(array $data)
    {
        static::validateNotificationData($data);
        $this->data = $data[0]['entry'];
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

    /**
     * Validate required data in the MPX notification error.
     *
     * @param array $data The array of data returned by MPX.
     *
     * @throws \InvalidArgumentException Thrown if a required key in $data is missing.
     *
     * @see https://docs.theplatform.com/help/wsf-subscribing-to-change-notifications#tp-toc25
     */
    public static function validateNotificationData($data)
    {
        $required = [
            'type',
            'entry',
        ];
        foreach ($required as $key) {
            if (empty($data[0][$key])) {
                throw new \InvalidArgumentException(sprintf('Required key %s is missing.', $key));
            }
        }

        static::validateData($data[0]['entry']);
    }

    /**
     * Parse a response into the exception.
     *
     * @param \Psr\Http\Message\ResponseInterface $response The response exception.
     *
     * @return string The message to use for the exception.
     */
    protected function parseResponse(ResponseInterface $response): string
    {
        $data = \GuzzleHttp\json_decode($response->getBody(), true);
        isset($data[0]) ? $this->setNotificationData($data) : $this->setData($data);
        $message = sprintf('HTTP %s Error %s: %s', $response->getStatusCode(), $this->data['title'], $this->data['description']);

        return $message;
    }
}
