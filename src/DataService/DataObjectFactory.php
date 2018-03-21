<?php

namespace Lullabot\Mpx\DataService;

use GuzzleHttp\Promise\PromiseInterface;
use Lullabot\Mpx\DataService\Access\Account;
use Lullabot\Mpx\DataService\Annotation\DataService;
use Lullabot\Mpx\Service\AccessManagement\ResolveDomain;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Factory to construct new data service objects from MPX.
 *
 * @todo link to generic upstream docs.
 */
class DataObjectFactory
{
    /**
     * The resolver for MPX services.
     *
     * @var \Lullabot\Mpx\Service\AccessManagement\ResolveDomain
     */
    protected $resolveDomain;

    /**
     * The class and annotation to load data objects into.
     *
     * @var array
     */
    protected $description;

    /**
     * The user session to use when loading from MPX.
     *
     * @var \Lullabot\Mpx\Service\IdentityManagement\UserSession
     */
    protected $userSession;

    /**
     * The manager used to load implementation classes of data objects.
     *
     * @var \Lullabot\Mpx\DataService\DataServiceManager
     */
    protected $manager;

    /**
     * @var string
     */
    private $path;

    /**
     * DataObjectFactory constructor.
     *
     * @todo Merge manager and service parameters?
     * @todo Inject the resolveDomain() instead of constructing?
     *
     * @param \Lullabot\Mpx\DataService\DataServiceManager         $manager
     * @param \Lullabot\Mpx\Service\IdentityManagement\UserSession $userSession
     * @param string                                               $service
     */
    public function __construct(DataServiceManager $manager, UserSession $userSession, string $service, string $path)
    {
        $this->userSession = $userSession;
        $this->resolveDomain = new ResolveDomain($this->userSession);
        $this->manager = $manager;
        $this->description = $manager->getDataService($service, $path);
        $this->path = $path;
    }

    /**
     * Load a data object from MPX, returning a promise to it.
     *
     * @param int                                      $id      The numeric ID to load.
     * @param \Lullabot\Mpx\DataService\Access\Account $account
     *
     * @return PromiseInterface
     */
    public function loadByNumericId(int $id, Account $account = null)
    {
        /** @var DataService $annotation */
        $annotation = $this->description['annotation'];
        $base = $this->getBaseUri($account, $annotation);

        $uri = $base.'/'.$id;

        return $this->load($uri);
    }

    /**
     * Deserialize a JSON string into a class.
     *
     * @todo Inject the serializer in the constructor?
     *
     * @param string $class The full class name to create.
     * @param string $data  The JSON string to deserialize.
     *
     * @return object
     */
    public function deserialize(string $class, $data)
    {
        // @todo This is a total hack as MPX returns JSON with null values. Replace by ommitting in the normalizer.
        $j = \GuzzleHttp\json_decode($data, true);
        array_filter($j, function ($value) {
            return null !== $value;
        });
        $data = json_encode($j);
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->deserialize($data, $class, 'json');
    }

    /**
     * @param $uri
     * @return PromiseInterface
     */
    public function load($uri): PromiseInterface
    {
        /** @var DataService $annotation */
        $annotation = $this->description['annotation'];
        $options = [
            'query' => [
                'schema' => $annotation->getSchemaVersion(),
                'form' => 'cjson',
            ],
        ];

        $response = $this->userSession->requestAsync('GET', $uri, $options)->then(
            function (ResponseInterface $response) {
                return $this->deserialize($this->description['class'], $response->getBody());
            }
        );

        return $response;
    }

    public function select(array $byFields, Account $account): PromiseInterface
    {
        /** @var DataService $annotation */
        $annotation = $this->description['annotation'];
        $options = [
            'query' => [
                'schema' => $annotation->getSchemaVersion(),
                'form' => 'cjson',
            ] + $byFields,
        ];

        $uri = $this->getBaseUri($account, $annotation);
        $response = $this->userSession->requestAsync('GET', $uri, $options)->then(
            function (ResponseInterface $response) {
                return $this->deserialize($this->description['class'], $response->getBody());
            }
        );

        return $response;
    }

    /**
     * Get the base URI from an annotation or service registry.
     *
     * @param Account $account The account to use for service resolution.
     * @param DataService $annotation The annotation data is being loaded for.
     * @return string The base URI.
     */
    private function getBaseUri(Account $account, DataService $annotation): string
    {
        // Accounts are optional as you need to be able to load an account
        // before you can resolve services.
        // @todo Can we do this by calling ResolveAllUrls?
        if (!($base = $annotation->getBaseUri())) {
            $resolved = $this->resolveDomain->resolve($account);
            $base = $resolved->getUrl($annotation->getService()).$annotation->getPath();
        }

        return $base;
    }
}
