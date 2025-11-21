<?php

namespace Lullabot\Mpx\Service\AccessManagement;

use Lullabot\Mpx\Normalizer\UriNormalizer;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Resolve all URLs for a given service.
 *
 * In general, ResolveDomain should be used instead, as it can return all
 * services at once. However, it requires an Account context, so if you do not
 * have one available use this instead.
 *
 * @see ResolveDomain
 * @see https://docs.theplatform.com/help/wsf-resolveallurls-method
 */
class ResolveAllUrls extends ResolveBase
{
    /**
     * The URL of the method. Note that we hardcode the whole path as this is
     * where other services are bootstrapped from.
     */
    final public const RESOLVE_ALL_URLS_URL = 'https://access.auth.theplatform.com/web/Registry/resolveAllUrls';

    /**
     * While this method is not a data service, it still has a schema version.
     */
    final public const SCHEMA_VERSION = '1.0';

    /**
     * Fetch URLs and return the response.
     *
     * @param string $service The service to find URLs for, such as 'Media Data Service'.
     */
    public function resolve(string $service): ResolveAllUrlsResponse
    {
        $key = $this->cacheKey($service);
        $item = $this->cache->getItem($key);

        if ($item->isHit()) {
            return $item->get();
        }

        $options = [
            'query' => [
                'schema' => self::SCHEMA_VERSION,
                '_service' => $service,
            ],
        ];

        if ($this->authenticatedClient->hasAccount()) {
            $options['query']['account'] = (string) $this->authenticatedClient->getAccount()->getMpxId();
        }

        $response = $this->authenticatedClient->request('GET', static::RESOLVE_ALL_URLS_URL, $options);

        $encoders = [new JsonEncoder()];
        $normalizers = [new UriNormalizer(), new ObjectNormalizer(null, null, null, new PhpDocExtractor()), new ArrayDenormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        /** @var \Lullabot\Mpx\Service\AccessManagement\ResolveAllUrlsResponse $resolved */
        $resolved = $serializer->deserialize($response->getBody(), ResolveAllUrlsResponse::class, 'json');
        $resolved->setService($service);
        $this->saveCache($key, $resolved);

        return $resolved;
    }

    protected function cacheKey(string $key_part): string
    {
        $key = md5('resolveAllUrls'.$key_part.self::SCHEMA_VERSION);

        return $key;
    }
}
