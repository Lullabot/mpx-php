<?php

namespace Lullabot\Mpx\Service\AccessManagement;

use Lullabot\Mpx\DataService\Account\Account;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;

class ResolveDomain
{
    const RESOLVE_DOMAIN_URL = 'https://access.auth.theplatform.com/web/Registry/resolveDomain';

    const SCHEMA_VERSION = '1.0';

    /**
     * @var \Lullabot\Mpx\Service\IdentityManagement\UserSession
     */
    private $userSession;

    public function __construct(UserSession $userSession)
    {
        $this->userSession = $userSession;
    }

    public function resolve(Account $account)
    {
        $options = [
            'query' => [
                'schema' => static::getSchemaVersion(),
                '_accountId' => (string) $account->getId(),
                'account' => (string) $account->getId(),
            ],
        ];

        $data = \GuzzleHttp\json_decode($this->userSession->request('GET', static::RESOLVE_DOMAIN_URL, $options)->getBody(), true);

        return new ResolveDomainResponse($data);
    }
}
