<?php

namespace Lullabot\Mpx\Service\AccessManagement;

/**
 * A response of all service domains.
 */
class ResolveDomainResponse
{
    /**
     * The array of resolveDomainResponse domains, indexed by their service name.
     *
     * @var \GuzzleHttp\Psr7\Uri[]
     */
    protected $resolveDomainResponse;

    /**
     * Return all resolveDomainResponse domains.
     *
     * @return \GuzzleHttp\Psr7\Uri[]
     */
    public function getResolveDomainResponse(): array
    {
        return $this->resolveDomainResponse;
    }

    /**
     * Get the URL for a given service.
     *
     * @param string $service The name of the service, such as 'Media Data Service read-only'.
     *
     * @return string The URL for the service.
     *
     * @throws \RuntimeException When the service is not found.
     */
    public function getUrl(string $service): string
    {
        if (!isset($this->resolveDomainResponse[$service])) {
            throw new \RuntimeException(sprintf('%s service was not found.', $service));
        }

        return $this->resolveDomainResponse[$service];
    }

    /**
     * @param \GuzzleHttp\Psr7\Uri[] $resolveDomainResponse
     */
    public function setResolveDomainResponse(array $resolveDomainResponse)
    {
        $this->resolveDomainResponse = $resolveDomainResponse;
    }
}
