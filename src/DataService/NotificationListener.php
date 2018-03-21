<?php

namespace Lullabot\Mpx\DataService;

use Lullabot\Mpx\Service\AccessManagement\ResolveAllUrls;
use Lullabot\Mpx\Service\AccessManagement\ResolveDomain;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Psr\Http\Message\ResponseInterface;

/**
 * Listen to events from MPX.
 */
class NotificationListener
{
    /**
     * @var ResolveDomain
     */
    protected $resolveDomain;

    protected $uri;

    /**
     * @var UserSession
     */
    private $session;

    /**
     * @var string
     */
    private $service;

    /**
     * @var string
     */
    private $clientId;

    public function __construct(string $clientId, UserSession $session, string $service)
    {
        $this->session = $session;
        $this->service = $service;
        $this->resolveDomain = new ResolveDomain($this->session);
        $this->clientId = $clientId;

        /** @var ResolveAllUrls $resolver */
        $resolver = ResolveAllUrls::load($this->session, $this->service)->wait();
        $this->uri = $resolver->resolve().'/notify';
    }

    public function sync()
    {
        // @todo Add support for filtering.
        return $this->session->requestAsync('GET', $this->uri, [
            'query' => [
                'clientId' => $this->clientId,
                'form' => 'cjson',
                'schema' => '1.10',
            ],
        ])->then(function (ResponseInterface $response) {
            $data = \GuzzleHttp\json_decode($response->getBody(), true);

            return $data[0]['id'];
        });
    }

    public function listen(int $since)
    {
        return $this->session->requestAsync('GET', $this->uri, [
            'query' => [
                'clientId' => $this->clientId,
                'since' => $since,
                'block' => 'true',
                'form' => 'cjson',
                'schema' => '1.10',
            ],
        ])->then(function (ResponseInterface $response) {
            $data = \GuzzleHttp\json_decode($response->getBody(), true);
            $manager = DataServiceManager::basicDiscovery();
            $objects = [];
            $dof = new DataObjectFactory($manager, $this->session, $this->service, '/data/Media');
            foreach ($data as $result) {
                if ('Media' == $result['type']) {
                    $objects[] = $dof->load($result['entry']['id']);
                }
            }

            return $objects;
        });
    }
}
