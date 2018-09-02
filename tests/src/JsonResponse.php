<?php

namespace Lullabot\Mpx\Tests;

use GuzzleHttp\Psr7\Response;

/**
 * A JSON response implementation.
 */
class JsonResponse extends Response
{
    /**
     * {@inheritdoc}
     */
    public function __construct($status = 200, array $headers = [], $body = null, $version = '1.1', $reason = null)
    {
        if (isset($body)) {
            if (\is_string($body) && is_file($body)) {
                $body = fopen($body, 'rb');
            } elseif (\is_string($body) && is_file(__DIR__.'/../fixtures/'.$body)) {
                $body = fopen(__DIR__.'/../fixtures/'.$body, 'rb');
            } elseif (\is_array($body)) {
                $body = \GuzzleHttp\json_encode($body);
            }
        }
        $headers += [
            'Content-Type' => 'application/json',
        ];
        parent::__construct($status, $headers, $body, $version, $reason);
    }
}
