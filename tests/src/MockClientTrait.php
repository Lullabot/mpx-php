<?php

namespace Mpx\Tests;

use GuzzleHttp\Client as GuzzleClient;
use Mpx\Client;
use GuzzleHttp\Handler\MockHandler;

trait MockClientTrait {

    /**
     * Constructs a mock Mpx client.
     *
     * @param array $handler_queue
     *   An optional array of requests if provided, will be passed into a new
     *   \GuzzleHttp\Handler\MockHandler object an added as $options['handler'].
     * @param array $options
     *   An optional array of client configuration.
     *
     * @return \Mpx\ClientInterface
     *   A configured Mpx client.
     */
    protected function getMockClient(array $handler_queue = [], array $options = []) {
        $options += [
            'handler' => $handler_queue ? new MockHandler($handler_queue) : NULL,
        ];
        $guzzleClient = new GuzzleClient($options);
        return new Client($guzzleClient);
    }

}
