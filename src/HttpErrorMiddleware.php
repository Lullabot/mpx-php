<?php

namespace Lullabot\Mpx;

use Lullabot\Mpx\Exception\MpxExceptionFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpErrorMiddleware
{
    public static function invoke()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                return $handler($request, $options)->then(
                    function (ResponseInterface $response) use ($request, $handler) {
                        // While it's not documented, we want to be sure that
                        // any 4XX or 5XX errors that break through suppression
                        // are still caught. In other words, this handler should
                        // be executed after the normal Guzzle error handler.

                        // If our response isn't JSON, we can't parse it.
                        $contentType = $response->getHeaderLine('Content-Type');
                        $data = \GuzzleHttp\json_decode($response->getBody(), true);
                        if (false === preg_match('~^(application|text)/json~', $contentType)) {
                            return $response;
                        }
                        if (empty($data['responseCode']) && empty($data['isException'])) {
                            return $response;
                        }

                        throw MpxExceptionFactory::create($request, $response);
                    }
                );
            };
        };
    }
}
