<?php

namespace Lullabot\Mpx\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MpxExceptionFactory
{
    public static function create(
        RequestInterface $request,
        ResponseInterface $response = null,
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
