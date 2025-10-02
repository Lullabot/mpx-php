<?php

namespace Lullabot\Mpx\Exception;

use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Exception when a client error is encountered (4xx codes).
 */
class ClientException extends GuzzleClientException implements MpxExceptionInterface
{
    use MpxExceptionTrait;

    /**
     * Construct a new ClientException.
     *
     * @param \Psr\Http\Message\RequestInterface       $request        The request that generated the error.
     * @param \Psr\Http\Message\ResponseInterface|null $response       The error response.
     * @param \Exception|null                          $previous       (optional) The previous exception.
     * @param array                                    $handlerContext (optional) Custom HTTP handler context, if available.
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, ?\Exception $previous = null, array $handlerContext = [])
    {
        $message = $this->parseResponse($response);
        parent::__construct($message, $request, $response, $previous, $handlerContext);
    }
}
