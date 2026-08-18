<?php

namespace Lullabot\Mpx\Exception;

/**
 * Interface for both client (4XX) and server (5XX) MPX exceptions.
 *
 * @see https://docs.theplatform.com/help/wsf-handling-data-service-exceptions
 * @see MpxExceptionTrait
 */
interface MpxExceptionInterface
{
    /**
     * Return the title of the MPX error.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Return the description of the MPX error.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Return the Correlation ID of the error.
     *
     * @return string
     */
    public function getCorrelationId();

    /**
     * Return the full stack trace of the error.
     *
     * @return string
     */
    public function getServerStackTrace();

    /**
     * Return the complete error object returned by MPX.
     *
     * @return array
     */
    public function getData();
}
