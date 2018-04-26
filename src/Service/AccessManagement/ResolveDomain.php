<?php

namespace Lullabot\Mpx\Service\AccessManagement;

use Lullabot\Mpx\AuthenticatedClient;
use Lullabot\Mpx\DataService\Access\Account;
use Lullabot\Mpx\DataService\IdInterface;
use Lullabot\Mpx\Normalizer\UriNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Resolve all service URLs for an account.
 *
 * @see https://docs.theplatform.com/help/wsf-resolvedomain-method
 */
class ResolveDomain
{
    /**
     * The method endpoint (this is not a true REST service).
     */
    const RESOLVE_DOMAIN_URL = 'https://access.auth.theplatform.com/web/Registry/resolveDomain';

    /**
     * The schema version of resolveDomain.
     */
    const SCHEMA_VERSION = '1.0';

    /**
     * @var \Lullabot\Mpx\AuthenticatedClient
     */
    private $authenticatedClient;

    public function __construct(AuthenticatedClient $authenticatedClient)
    {
        $this->authenticatedClient = $authenticatedClient;
    }

    /**
     * Resolve all URLs for an account.
     *
     * @param IdInterface $account The account to resolve service URLs for.
     *
     * @return ResolveDomainResponse A response with the service URLs.
     */
    public function resolve(IdInterface $account)
    {
        $options = [
            'query' => [
                'schema' => static::SCHEMA_VERSION,
                '_accountId' => (string) $account->getId(),
                'account' => (string) $account->getId(),
            ],
        ];

        $response = $this->authenticatedClient->request('GET', static::RESOLVE_DOMAIN_URL, $options);

        $encoders = [new JsonEncoder()];
        $normalizers = [new UriNormalizer(), new ObjectNormalizer(null, null, null, new \Lullabot\Mpx\DataService\ResolveDomainResponseExtractor()), new ArrayDenormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->deserialize($response->getBody(), ResolveDomainResponse::class, 'json');
    }
}
