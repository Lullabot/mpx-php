<?php

namespace Lullabot\Mpx\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Factory to generate MPX API exceptions.
 */
class MpxExceptionFactory
{
    /**
     * Create a new MPX API exception.
     */
    public static function create(
        RequestInterface $request,
        ResponseInterface $response,
        ?\Exception $previous = null,
        array $ctx = [],
    ): ClientException|ServerException {
        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        ClientException::validateData($data);

        $altered = $response->withStatus($data['responseCode'], $data['title']);

        return self::createException($request, $altered, $previous, $ctx);
    }

    /**
     * Create a new MPX API exception from a notification.
     */
    public static function createFromNotificationException(RequestInterface $request, ResponseInterface $response, ?\Exception $previous = null, array $ctx = []): ClientException|ServerException
    {
        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        ServerException::validateNotificationData($data);

        $altered = $response->withStatus($data[0]['entry']['responseCode'], $data[0]['entry']['title']);

        return self::createException($request, $altered, $previous, $ctx);
    }

    /**
     * Create a client or server exception.
     */
    private static function createException(RequestInterface $request,
        ResponseInterface $altered,
        ?\Exception $previous = null,
        array $ctx = [],
    ): ClientException|ServerException {
        if ($altered->getStatusCode() >= 400 && $altered->getStatusCode() < 500) {
            return new ClientException($request, $altered, $previous, $ctx);
        }

        return new ServerException($request, $altered, $previous, $ctx);
    }
}
