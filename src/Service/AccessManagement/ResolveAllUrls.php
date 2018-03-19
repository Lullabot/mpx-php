<?php

namespace Lullabot\Mpx\Service\AccessManagement;

use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Psr\Http\Message\ResponseInterface;

/**
 * Resolve all URLs for a given service.
 *
 * @see https://docs.theplatform.com/help/wsf-resolveallurls-method
 */
class ResolveAllUrls
{
    /**
     * While this method is not a data service, it still has a schema version.
     */
    const SCHEMA_VERSION = '1.0';

    /**
     * The URL of the method. Note that we hardcode the whole path as this is
     * where other services are bootstrapped from.
     */
    const RESOLVE_ALL_URLS_URL = 'https://access.auth.theplatform.com/web/Registry/resolveAllUrls';

    /**
     * An array of resolved URLs for the service.
     *
     * @var string[]
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
     * @param string $service The service that was queried, such as 'Access Data Service'.
     * @param array  $data    The MPX response.
     */
    public function __construct(string $service, array $data)
    {
        if (!isset($data['resolveAllUrlsResponse'])) {
            throw new \InvalidArgumentException();
        }

        $this->resolved = $data['resolveAllUrlsResponse'];
        $this->service = $service;
    }

    /**
     * Fetch URLs and return the response.
     *
     * @param \Lullabot\Mpx\Service\IdentityManagement\UserSession $userSession The authenticated session to use when querying.
     * @param string                                               $service     The service to find URLs for, such as 'Media Data Service'.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface A promise to return a new ResolveAllUrls class.
     */
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
