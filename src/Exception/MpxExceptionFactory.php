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
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Exception|NULL $previous
     * @param array $ctx
     *
     * @return \Lullabot\Mpx\Exception\MpxExceptionInterface
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

        if ($altered->getStatusCode() >= 400 && $altered->getStatusCode() < 500) {
            return new ClientException($data, $request, $altered, $previous, $ctx);
        }

        return new ServerException($data, $request, $altered, $previous, $ctx);
    }
}
