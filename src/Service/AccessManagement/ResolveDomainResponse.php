<?php

namespace Lullabot\Mpx\Service\AccessManagement;

class ResolveDomainResponse
{
    protected $resolved;

    public function __construct(array $resolved)
    {
        $this->resolved = $resolved['resolveDomainResponse'];
    }

    /**
     * @return array
     */
    public function getResolved(): array
    {
        return $this->resolved;
    }

    public function getUrl(string $service): string
    {
        if (!isset($this->resolved[$service])) {
            throw new \Exception();
        }

        return $this->resolved[$service];
    }
}
