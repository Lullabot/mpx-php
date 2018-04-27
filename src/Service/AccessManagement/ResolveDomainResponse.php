<?php

namespace Lullabot\Mpx\Service\AccessManagement;

use GuzzleHttp\Psr7\Uri;

/**
 * A response of all service domains.
 */
class ResolveDomainResponse
{
    /**
     * The array of resolveDomainResponse domains, indexed by their service name.
     *
     * @var Uri[]
     */
    protected $resolveDomainResponse;

    /**
     * Return all available services.
     *
     * @return string[]
     */
    public function getServices(): array
    {
        return array_keys($this->resolveDomainResponse);
    }

    /**
     * Get the URL for a given service.
     *
     * Note that no 'getResolveDomainResponse' method is implemented, to ensure
     * that callers get https URLs unless they explicitly ask for insecure URLs.
     *
     * While resolveDomainResponse currently returns many services with http
     * URLs, according to thePlatform all services should now support https.
     *
     * @param string $service  The name of the service, such as 'Media Data Service read-only'.
     * @param bool   $insecure (optional) Set to true to request the insecure version of this service.
     *
     * @return Uri The URL for the service.
     */
    public function getUrl(string $service, bool $insecure = false): Uri
    {
        if (!isset($this->resolveDomainResponse[$service])) {
            throw new \RuntimeException(sprintf('%s service was not found.', $service));
        }

        $url = $this->resolveDomainResponse[$service];
        if (!$insecure) {
            $url = $url->withScheme('https');
        }

        return $url;
    }

    /**
     * @param Uri[] $resolveDomainResponse
     */
    public function setResolveDomainResponse(array $resolveDomainResponse)
    {
        $this->resolveDomainResponse = $resolveDomainResponse;
    }
}
