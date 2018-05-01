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
     *
     * @param \Psr\Http\Message\RequestInterface  $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Exception|null                     $previous
     * @param array                               $ctx
     *
     * @return \Lullabot\Mpx\Exception\ClientException|\Lullabot\Mpx\Exception\ServerException
     */
    public static function create(
        RequestInterface $request,
        ResponseInterface $response,
        \Exception $previous = null,
        array $ctx = []
    ) {
        $data = \GuzzleHttp\json_decode($response->getBody(), true);
        MpxExceptionTrait::validateData($data);

        $altered = $response->withStatus($data['responseCode'], $data['title']);
        return self::createException($request, $altered, $previous, $ctx);
    }

    /**
     * Create a new MPX API exception from a notification.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param \Exception|null   $previous
     * @param array             $ctx
     *
     * @return ClientException|ServerException
     */
    public static function createFromNotificationException(RequestInterface $request, ResponseInterface $response, \Exception $previous = null, array $ctx = [])
    {
        $data = \GuzzleHttp\json_decode($response->getBody(), true);
        MpxExceptionTrait::validateNotificationData($data);

        $altered = $response->withStatus($data[0]['entry']['responseCode'], $data[0]['entry']['title']);
        return self::createException($request, $altered, $previous, $ctx);
    }

    /**
     * Create a client or server exception.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $altered
     *
     * @param \Exception $previous
     * @param array $ctx
     *
     * @return ClientException|ServerException
     */
    private static function createException(RequestInterface $request,
        ResponseInterface $altered,
        \Exception $previous = null,
        array $ctx = []
    ) {
        if ($altered->getStatusCode() >= 400 && $altered->getStatusCode() < 500) {
            return new ClientException($request, $altered, $previous, $ctx);
        }

        return new ServerException($request, $altered, $previous, $ctx);
    }
}
