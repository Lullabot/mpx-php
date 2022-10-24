<?php

namespace Lullabot\Mpx\DataService;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Lullabot\Mpx\AuthenticatedClient;
use Lullabot\Mpx\Normalizer\UnixMillisecondNormalizer;
use Lullabot\Mpx\Normalizer\UriNormalizer;
use Lullabot\Mpx\Service\AccessManagement\ResolveAllUrls;
use Lullabot\Mpx\Service\AccessManagement\ResolveDomain;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Listen to events from MPX.
 *
 * @see https://docs.theplatform.com/help/wsf-subscribing-to-change-notifications
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
     * The client for authenticated requests.
     *
     * @var \Lullabot\Mpx\AuthenticatedClient
     */
    protected $authenticatedClient;

    /**
     * The service to listen to notifications on, such as /data/Media from the Media Data Service.
     *
     * @var DiscoveredDataService
     */
    protected $service;

    /**
     * @var string
     */
    private $clientId;

    /**
     * NotificationListener constructor.
     *
     * This method always uses the read-only version of a service.
     *
     * @see https://docs.theplatform.com/help/wsf-subscribing-to-change-notifications#tp-toc2
     *
     * @param \Lullabot\Mpx\AuthenticatedClient $session       The client to use for authenticated requests.
     * @param DiscoveredDataService             $service       The name of the service to listen to notifications on, such as 'Media Data Service'.
     * @param string                            $clientId      A string to identify this client in debugging.
     * @param CacheItemPoolInterface|null       $cacheItemPool (optional) The cache for API metadata.
     */
    public function __construct(AuthenticatedClient $session, DiscoveredDataService $service, string $clientId, CacheItemPoolInterface $cacheItemPool = null)
    {
        $this->clientId = $clientId;
        $this->authenticatedClient = $session;
        $this->service = $service;

        if (!$cacheItemPool) {
            $cacheItemPool = new ArrayCachePool();
        }

        $this->resolveDomain = new ResolveDomain($this->authenticatedClient, $cacheItemPool);
        $resolver = new ResolveAllUrls($this->authenticatedClient, $cacheItemPool);

        $response = $resolver->resolve($this->service->getAnnotation()->getService(true));
        $this->uri = $response->getUrl().'/notify';
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
        $query = [
            'clientId' => $this->clientId,
            'form' => 'cjson',
            'schema' => '1.10',
        ];

        if ($this->authenticatedClient->hasAccount()) {
            $query['account'] = (string) $this->authenticatedClient->getAccount()->getMpxId();
        }

        return $this->authenticatedClient->requestAsync('GET', $this->uri, [
            'query' => $query,
        ])->then(function (ResponseInterface $response) {
            $data = \GuzzleHttp\json_decode($response->getBody(), true);

            return $data[0]['id'];
        });
    }

    /**
     * Listen for notifications.
     *
     * This method always filters to the object type defined in the discovered
     * data service passed in the constructor, such as 'Media'.
     *
     * @see  https://docs.theplatform.com/help/media-media-data-service-api-reference
     *
     * @todo Add support for a configurable timeout?
     *
     * @see  \Lullabot\Mpx\DataService\NotificationListener::sync
     * @see  \Lullabot\Mpx\DataService\Notification
     *
     * @param int $since   The last sync ID.
     * @param int $maximum (optional) The maximum number of notifications to return. Defaults to 500.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface A promise returning an array of Notification objects.
     */
    public function listen(int $since, int $maximum = 500)
    {
        $query = [
            'clientId' => $this->clientId,
            'since' => $since,
            'block' => 'true',
            'form' => 'cjson',
            'schema' => '1.10',
            'filter' => $this->service->getAnnotation()->getObjectType(),
            'size' => $maximum,
        ];

        if ($this->authenticatedClient->hasAccount()) {
            $query['account'] = (string) $this->authenticatedClient->getAccount()->getMpxId();
        }

        return $this->authenticatedClient->requestAsync('GET', $this->uri, [
            'query' => $query,
        ])->then(function (ResponseInterface $response) {
            return $this->deserializeResponse($response);
        });
    }

    /**
     * Deserialize a notification response.
     *
     * @return Notification
     */
    protected function deserializeResponse(ResponseInterface $response)
    {
        // First, we need an encoder that filters out null values.
        $encoders = [new JsonEncoder()];

        // Attempt normalizing each key in this order, including denormalizing recursively.
        $extractor = NotificationTypeExtractor::create();
        $extractor->setClass($this->service->getClass());
        $normalizers = [
            new UnixMillisecondNormalizer(),
            new UriNormalizer(),
            new ObjectNormalizer(null, null, null, $extractor),
            new ArrayDenormalizer(),
        ];

        $serializer = new Serializer($normalizers, $encoders);
        // @todo Handle exception returns.
        return $serializer->deserialize($response->getBody(), Notification::class.'[]', 'json');
    }
}
