<?php

namespace Lullabot\Mpx\Exception;

use GuzzleHttp\Exception\ClientException as GuzzleClientException;

class ClientException extends GuzzleClientException {

    use MpxExceptionTrait;

    public function __construct($data, \Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response = NULL, \Exception $previous = NULL, array $handlerContext = []) {
        $this->setData($data);
        $message = sprintf("Error %s: %s", $data['title'], $data['description']);
        parent::__construct($message, $request, $response, $previous, $handlerContext);
    }
}
