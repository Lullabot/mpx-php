<?php

namespace Lullabot\Mpx\DataService;

use Lullabot\Mpx\Service\AccessManagement\ResolveAllUrls;
use Lullabot\Mpx\Service\AccessManagement\ResolveDomain;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Psr\Http\Message\ResponseInterface;

/**
 * Listen to events from MPX.
 *
 * @see https://docs.theplatform.com/help/wsf-subscribing-to-change-notifications
 *
 * @codeCoverageIgnore Until we refactor this to allow loading all objects.
 */
class NotificationListener
{
    /**
     * The resolver for MPX services.
     *
     * @var ResolveDomain
     */
    protected $resolveDomain;

    /**
     * The notification URL.
     *
     * @var string
     */
    protected $uri;

    /**
     * The user session to use for authenticated requests.
     *
     * @var UserSession
     */
    protected $session;

    /**
     * The name of the service to listen to notifications on, such as 'Media Data Service'.
     *
     * @var string
     */
    protected $service;

    /**
     * @var string
     */
    private $clientId;

    /**
     * NotificationListener constructor.
     *
     * @param string      $clientId A string to identify this client in debugging.
     * @param UserSession $session  The user session to use for authenticated requests.
     * @param string      $service  The name of the service to listen to notifications on, such as 'Media Data Service'.
     */
    public function __construct(string $clientId, UserSession $session, string $service)
    {
        $this->clientId = $clientId;
        $this->session = $session;
        $this->service = $service;

        $this->resolveDomain = new ResolveDomain($this->session);

        /** @var ResolveAllUrls $resolver */
        $resolver = ResolveAllUrls::load($this->session, $this->service)->wait();
        $this->uri = $resolver->resolve().'/notify';
    }

    /**
     * Return the last available sync ID to synchronize notifications.
     *
     * @see https://docs.theplatform.com/help/wsf-subscribing-to-change-notifications#tp-toc10
     *
     * @return \GuzzleHttp\Promise\PromiseInterface A promise to return the last sync ID as an integer.
     */
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

    /**
     * Listen for notifications.
     *
     * @todo Add support for a configurable timeout?
     * @todo Convert the return to an ObjectList.
     *
     * @see \Lullabot\Mpx\DataService\NotificationListener::sync
     *
     * @param int $since The last sync ID.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface A promise returning a list of notified objects.
     */
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
            // @todo Fix only being able to load into Media objects.
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