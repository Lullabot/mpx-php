<?php

namespace Lullabot\Mpx\Exception;

use GuzzleHttp\Exception\ServerException as GuzzleServerException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ServerException extends GuzzleServerException implements MpxExceptionInterface
{
    use MpxExceptionTrait;

    /**
     * Construct a new ClientException.
     *
     * @param RequestInterface       $request        The request that generated the error.
     * @param ResponseInterface|null $response       The error response.
     * @param \Exception|null        $previous       (optional) The previous exception.
     * @param array                  $handlerContext (optional) Custom HTTP handler context, if available.
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, ?\Exception $previous = null, array $handlerContext = [])
    {
        $message = $this->parseResponse($response);
        parent::__construct($message, $request, $response, $previous, $handlerContext);
    }
}
