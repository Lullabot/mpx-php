<?php

namespace Lullabot\Mpx\DataService;

use GuzzleHttp\Promise\PromiseInterface;
use Lullabot\Mpx\DataService\Access\Account;
use Lullabot\Mpx\DataService\Annotation\DataService;
use Lullabot\Mpx\Encoder\CJsonEncoder;
use Lullabot\Mpx\Normalizer\UnixMicrosecondNormalizer;
use Lullabot\Mpx\Normalizer\UriNormalizer;
use Lullabot\Mpx\Service\AccessManagement\ResolveDomain;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
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
     * DataObjectFactory constructor.
     *
     * @todo Inject the resolveDomain() instead of constructing?
     *
     * @param array                                                $description The array describing the destination class for this factory.
     * @param \Lullabot\Mpx\Service\IdentityManagement\UserSession $userSession
     */
    public function __construct(array $description, UserSession $userSession)
    {
        $this->userSession = $userSession;
        $this->resolveDomain = new ResolveDomain($this->userSession);
        $this->description = $description;
    }

    /**
     * Load a data object from MPX, returning a promise to it.
     *
     * @param int                                      $id       The numeric ID to load.
     * @param \Lullabot\Mpx\DataService\Access\Account $account
     * @param bool                                     $readonly (optional) Load from the read-only service.
     *
     * @return PromiseInterface
     */
    public function loadByNumericId(int $id, Account $account = null, bool $readonly = false)
    {
        /** @var DataService $annotation */
        $annotation = $this->description['annotation'];
        $base = $this->getBaseUri($account, $annotation, $readonly);

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
        return $this->getObjectSerializer()->deserialize($data, $class, 'json');
    }

    /**
     * @param $uri
     *
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

    /**
     * Query for MPX data using 'byField' parameters.
     *
     * @param array   $byFields The fields and values to filter by. Note these are exact matches.
     * @param Account $account  The account context to use in the request.
     *
     * @return PromiseInterface A promise returning a \Lullabot\Mpx\DataService\ResultList.
     */
    public function select(array $byFields, Account $account): PromiseInterface
    {
        /** @var DataService $annotation */
        $annotation = $this->description['annotation'];
        $options = [
            'query' => $byFields + [
                'schema' => $annotation->getSchemaVersion(),
                'form' => 'cjson',
            ],
        ];

        $uri = $this->getBaseUri($account, $annotation);

        $response = $this->userSession->requestAsync('GET', $uri, $options)->then(
            function (ResponseInterface $response) {
                $data = $response->getBody();

                return $this->getEntriesSerializer()->deserialize($data, ResultList::class, 'json');
            }
        );

        return $response;
    }

    /**
     * Get the base URI from an annotation or service registry.
     *
     * @param Account     $account    The account to use for service resolution.
     * @param DataService $annotation The annotation data is being loaded for.
     * @param bool                                     $readonly (optional) Load from the read-only service.
     *
     * @return string The base URI.
     */
    private function getBaseUri(Account $account = null, DataService $annotation, bool $readonly = false): string
    {
        // Accounts are optional as you need to be able to load an account
        // before you can resolve services.
        // @todo Can we do this by calling ResolveAllUrls?
        if (!($base = $annotation->getBaseUri())) {
            $resolved = $this->resolveDomain->resolve($account);
            $base = $resolved->getUrl($annotation->getService($readonly)).$annotation->getPath();
        }

        return $base;
    }

    private function getEntriesSerializer()
    {
        // We need a property extractor that understands the varying types of 'entries'.
        // @todo Should we just make multiple subclasses of ResultList?
        $dataServiceExtractor = new DataServiceExtractor();
        $dataServiceExtractor->setClass($this->description['class']);

        return $this->getObjectSerializer($dataServiceExtractor);
    }

    /**
     * @param null $dataServiceExtractor
     *
     * @return Serializer
     */
    private function getObjectSerializer(PropertyTypeExtractorInterface $dataServiceExtractor): Serializer
    {
        // First, we need an encoder that filters out null values.
        $encoders = [new CJsonEncoder()];

        // Attempt normalizing each key in this order, including denormalizing recursively.
        $normalizers = [
            new UnixMicrosecondNormalizer(),
            new UriNormalizer(),
            new ObjectNormalizer(
                null, null, null,
                $dataServiceExtractor
            ),
            new ArrayDenormalizer(),
        ];

        return new Serializer($normalizers, $encoders);
    }
}
