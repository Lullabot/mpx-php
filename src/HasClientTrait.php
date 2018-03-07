<?php

namespace Lullabot\Mpx;

use GuzzleHttp\Client as GuzzleClient;

trait HasClientTrait
{
    /**
     * @var \Lullabot\Mpx\Client
     */
    protected $client;

    /**
     * Sets a HTTP client.
     *
     * @param \Lullabot\Mpx\Client $client
     *
     * @return static
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return \Lullabot\Mpx\Client $client
     */
    public function getClient()
    {
        if (!isset($this->client)) {
            $this->client = new Client(new GuzzleClient());
        }

        return $this->client;
    }
}
