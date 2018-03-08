<?php

namespace Lullabot\Mpx\Exception;

use GuzzleHttp\Exception\ServerException as GuzzleServerException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ServerException extends GuzzleServerException
{
    use MpxExceptionTrait;

    public function __construct($data, RequestInterface $request, ResponseInterface $response = null, \Exception $previous = null, array $handlerContext = [])
    {
        $this->setData($data);
        $message = sprintf('Error %s: %s', $data['title'], $data['description']);
        parent::__construct($message, $request, $response, $previous, $handlerContext);
    }
}
