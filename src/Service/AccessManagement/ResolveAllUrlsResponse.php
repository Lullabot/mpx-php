<?php

namespace Lullabot\Mpx\Service\AccessManagement;

use Psr\Http\Message\UriInterface;

class ResolveAllUrlsResponse
{
    /**
     * An array of resolvedUrls URLs for the service.
     *
     * @var UriInterface[]
     */
    protected $resolveAllUrlsResponse = [];

    /**
     * The service these URLs correspond to, such as 'Media Data Service'.
     *
     * @var string
     */
    protected $service;

    /**
     * @param UriInterface[] $resolvedAllUrls
     */
    public function setResolveAllUrlsResponse(array $resolvedAllUrls)
    {
        $this->resolveAllUrlsResponse = $resolvedAllUrls;
    }

    /**
     * @param string $service
     */
    public function setService(string $service)
    {
        $this->service = $service;
    }

    /**
     * @param bool $insecure (optional) Set to true to request the insecure version of this service.
     *
     * @return UriInterface
     */
    public function resolve(bool $insecure = false): UriInterface
    {
        $url = $this->resolveAllUrlsResponse[array_rand($this->resolveAllUrlsResponse)];

        if ($insecure) {
            return $url;
        }

        return $url->withScheme('https');
    }

    /**
     * @return string
     */
    public function getService(): string
    {
        return $this->service;
    }
}
