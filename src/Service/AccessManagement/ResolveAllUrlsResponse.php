<?php

namespace Lullabot\Mpx\Service\AccessManagement;

use Psr\Http\Message\UriInterface;

/**
 * This class represents a response from mpx's resolveAllUrls API.
 *
 * In concept, this class is similar to all Data Service classes in that it is
 * used as a destination class for the Serializer. Note that these methods are
 * not part of a Data Service, so common assumptions like paths, schemas, and
 * data structures do not directly apply.
 *
 * In general, use resolveDomain instead as it is more efficient.
 *
 * @see \Lullabot\Mpx\Service\AccessManagement\ResolveDomain
 * @see https://docs.theplatform.com/help/wsf-resolveallurls-method
 */
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
     * Return the resolved URL for this service.
     *
     * @param bool $insecure (optional) Set to true to request the insecure version of this service.
     *
     * @return UriInterface
     */
    public function getUrl(bool $insecure = false): UriInterface
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
