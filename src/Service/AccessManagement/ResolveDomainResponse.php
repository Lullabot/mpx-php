<?php

namespace Lullabot\Mpx\Service\AccessManagement;

/**
 * A response of all service domains.
 */
class ResolveDomainResponse
{
    /**
     * The array of resolved domains, indexed by their service name.
     *
     * @var array
     */
    protected $resolved;

    /**
     * ResolveDomainResponse constructor.
     *
     * @param array $resolved The array of data from the resolveDomain response.
     */
    public function __construct(array $resolved)
    {
        if (empty($resolved['resolveDomainResponse'])) {
            throw new \InvalidArgumentException('The resolved data must contain a resolveDomainResponse key.');
        }

        $this->resolved = $resolved['resolveDomainResponse'];
    }

    /**
     * Return all resolved domains.
     *
     * @return array
     */
    public function getResolved(): array
    {
        return $this->resolved;
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
        if (!isset($this->resolved[$service])) {
            throw new \RuntimeException(sprintf('%s service was not found.', $service));
        }

        return $this->resolved[$service];
    }
}
