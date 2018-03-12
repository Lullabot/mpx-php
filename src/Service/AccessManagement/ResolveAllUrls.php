<?php

namespace Lullabot\Mpx\Service\AccessManagement;

use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Psr\Http\Message\ResponseInterface;

class ResolveAllUrls
{
    const SCHEMA_VERSION = '1.0';

    const RESOLVE_ALL_URLS_URL = 'https://access.auth.theplatform.com/web/Registry/resolveAllUrls';

    /**
     * An array of resolved URLs for the service.
     *
     * @var array
     */
    protected $resolved;

    /**
     * The service these URLs correspond to, such as 'Media Data Service'.
     *
     * @var string
     */
    protected $service;

    /**
     * ResolveAllUrls constructor.
     *
     * @todo Is there value in storing Responses in all classes?
     *
     * @param string $service
     * @param array  $data
     */
    public function __construct(string $service, array $data)
    {
        if (!isset($data['resolveAllUrlsResponse'])) {
            throw new \InvalidArgumentException();
        }

        $this->resolved = $data['resolveAllUrlsResponse'];
        $this->service = $service;
    }

    public static function load(UserSession $userSession, string $service)
    {
        $options = [
            'query' => [
                'schema' => static::SCHEMA_VERSION,
                '_service' => $service,
            ],
        ];

        return $userSession->requestAsync('GET', self::RESOLVE_ALL_URLS_URL, $options)->then(function (ResponseInterface $response) use ($service) {
            return new static($service, \GuzzleHttp\json_decode($response->getBody(), true));
        });
    }

    /**
     * Return the resolved URLs for this service.
     *
     * @return string[]
     */
    public function getResolved(): array
    {
        return $this->resolved;
    }

    public function resolve(): string
    {
        // If multiple URLs are returned, any of them are usable, so we choose
        // a random one.
        // @todo Double check this assumption.
        return $this->getResolved()[array_rand($this->getResolved())];
    }

    /**
     * Return the service these URLs correspond to, such as 'Media Data Service'.
     *
     * @return string
     */
    public function getService(): string
    {
        return $this->service;
    }
}
