<?php

namespace Lullabot\Mpx\Service\AccessManagement;

use Lullabot\Mpx\DataService\Access\Account;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;

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
     * @var \Lullabot\Mpx\Service\IdentityManagement\UserSession
     */
    private $userSession;

    public function __construct(UserSession $userSession)
    {
        $this->userSession = $userSession;
    }

    /**
     * Resolve all URLs for an account.
     *
     * @param Account $account The account to resolve service URLs for.
     *
     * @return ResolveDomainResponse A response with the service URLs.
     */
    public function resolve(Account $account)
    {
        $options = [
            'query' => [
                'schema' => static::SCHEMA_VERSION,
                '_accountId' => (string) $account->getId(),
                'account' => (string) $account->getId(),
            ],
        ];

        $data = \GuzzleHttp\json_decode($this->userSession->request('GET', static::RESOLVE_DOMAIN_URL, $options)->getBody(), true);

        return new ResolveDomainResponse($data);
    }
}
