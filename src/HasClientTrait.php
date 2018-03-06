<?php

namespace Mpx;

use GuzzleHttp\Client as GuzzleClient;

trait HasClientTrait
{
    /**
     * @var \Mpx\Client
     */
    protected $client;

    /**
     * Sets a HTTP client.
     *
     * @param \Mpx\Client $client
     *
     * @return static
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return \Mpx\Client $client
     */
    public function getClient()
    {
        if (!isset($this->client)) {
            $this->client = new Client(new GuzzleClient());
        }

        return $this->client;
    }
}
