<?php


namespace Lullabot\Mpx\Exception;


use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ClientException extends GuzzleClientException {

    public function __construct($message, RequestInterface $request, ResponseInterface $response = NULL, \Exception $previous = NULL, array $handlerContext = []) {
        parent::__construct($message, $request, $response, $previous, $handlerContext);
    }

    public static function create(
        RequestInterface $request,
        ResponseInterface $response = NULL,
        \Exception $previous = NULL,
        array $ctx = []
    ) {
        $data = \GuzzleHttp\json_decode($response->getBody(), true);
        // @todo Prior code also checked for $data being an array, but the docs
        // at https://docs.theplatform.com/help/wsf-handling-data-service-exceptions#tp-toc4
        // don't show that.
        $required = [
            'responseCode',
            'isException',
            'title',
            'description',
        ];
        foreach ($required as $key) {
            if (empty($data[$key])) {
                throw new \InvalidArgumentException(sprintf("Required key %s is missing.", $key));
            }
        }

        $message = sprintf("Error %s: %s", $data['title'], $data['description']);
        $altered = new Response((int) $data['responseCode'], $response->getHeaders(), $response->getBody(), $response->getProtocolVersion(), $response->getReasonPhrase());
        return new static($message, $request, $altered, $previous, $ctx);
    }

}
