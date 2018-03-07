<?php

namespace Lullabot\Mpx\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use Lullabot\Mpx\Client;

trait MockClientTrait {

    /**
     * Constructs a mock Lullabot\Mpx client.
     *
     * @param array $handler_queue
     *   An optional array of requests if provided, will be passed into a new
     *   \GuzzleHttp\Handler\MockHandler object an added as $options['handler'].
     * @param array $options
     *   An optional array of client configuration.
     *
     * @return \Lullabot\Mpx\Client
     *   A configured Lullabot\Mpx client.
     */
    protected function getMockClient(array $handler_queue = [], array $options = []) {
        $options += [
            'handler' => $handler_queue ? new MockHandler($handler_queue) : NULL,
        ];
        $guzzleClient = new GuzzleClient($options);
        return new Client($guzzleClient);
    }

}
