<?php

namespace Lullabot\Mpx;

use Lullabot\Mpx\Exception\MpxExceptionFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Guzzle middleware functions.
 */
class Middleware
{
    /**
     * A middleware to check for MPX errors in the body of the response.
     *
     * @see https://docs.theplatform.com/help/wsf-handling-data-service-exceptions
     *
     * @return \Closure A middleware function.
     */
    public static function mpxErrors(): \Closure
    {
        // Guzzle's built-in middlewares also have this level of nested
        // functions, so we follow the same pattern even though it's difficult
        // to read.
        return fn (callable $handler) => fn (RequestInterface $request, array $options) => // We only need to process after the request has been sent.
$handler($request, $options)->then(
            function (ResponseInterface $response) use ($request) {
                // While it's not documented, we want to be sure that
                // any 4XX or 5XX errors that break through suppression
                // are still parsed. In other words, this handler should
                // be executed before the normal Guzzle error handler.

                // If our response isn't JSON, we can't parse it.
                $contentType = $response->getHeaderLine('Content-Type');
                if (0 === preg_match('!^(application|text)\/json!', $contentType)) {
                    return $response;
                }

                $data = \GuzzleHttp\json_decode($response->getBody(), true);

                // Notification responses have a different exception format.
                if (isset($data[0]) && isset($data[0]['type']) && 'Exception' == $data[0]['type']) {
                    throw MpxExceptionFactory::createFromNotificationException($request, $response);
                }

                if (empty($data['responseCode']) && empty($data['isException'])) {
                    return $response;
                }

                throw MpxExceptionFactory::create($request, $response);
            }
        );
    }
}
