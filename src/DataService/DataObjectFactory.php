<?php

namespace Lullabot\Mpx\DataService;

use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\AuthenticatedClient;
use Lullabot\Mpx\Cache\Adapter\PHPArray\ArrayCachePool;
use Lullabot\Mpx\DataService\Annotation\DataService;
use Lullabot\Mpx\Encoder\CJsonEncoder;
use Lullabot\Mpx\Normalizer\CustomFieldsNormalizer;
use Lullabot\Mpx\Normalizer\UnixMillisecondNormalizer;
use Lullabot\Mpx\Normalizer\UriNormalizer;
use Lullabot\Mpx\Service\AccessManagement\ResolveAllUrls;
use Lullabot\Mpx\Service\AccessManagement\ResolveDomain;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\PropertyInfo\PropertyInfoCacheExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
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
     * @var ResolveDomain
     */
    protected $resolveDomain;

    /**
     * The class and annotation to load data objects into.
     *
     * @var DiscoveredDataService
     */
    protected $dataService;

    /**
     * The client to make authenticated API calls.
     *
     * @var AuthenticatedClient
     */
    protected $authenticatedClient;

    /**
     * Cache to store reflection metadata from implementing classes.
     *
     * @var CacheItemPoolInterface
     */
    protected $cacheItemPool;

    /**
     * DataObjectFactory constructor.
     *
     * @param DiscoveredDataService       $dataService         The service to load data from.
     * @param AuthenticatedClient         $authenticatedClient A client to make authenticated MPX calls.
     * @param CacheItemPoolInterface|null $cacheItemPool       (optional) Cache to store API metadata.
     */
    public function __construct(DiscoveredDataService $dataService, AuthenticatedClient $authenticatedClient, ?CacheItemPoolInterface $cacheItemPool = null)
    {
        $this->authenticatedClient = $authenticatedClient;
        $this->dataService = $dataService;

        if (!$cacheItemPool) {
            $cacheItemPool = new ArrayCachePool();
        }
        $this->cacheItemPool = $cacheItemPool;

        $this->resolveDomain = new ResolveDomain($this->authenticatedClient, $this->cacheItemPool);
    }

    /**
     * Load a data object from MPX, returning a promise to it.
     *
     * @param int   $id       The numeric ID to load.
     * @param bool  $readonly (optional) Load from the read-only service.
     * @param array $options  (optional) An array of HTTP client options.
     *
     * @return PromiseInterface
     */
    public function loadByNumericId(int $id, bool $readonly = false, array $options = [])
    {
        $annotation = $this->dataService->getAnnotation();
        $base = $this->getBaseUri($annotation, $readonly);

        $uri = new Uri($base.'/'.$id);

        return $this->load($uri, $options);
    }

    /**
     * Deserialize a JSON string into a class.
     *
     * @todo Inject the serializer in the constructor?
     *
     * @param string $data  The JSON string to deserialize.
     * @param string $class The full class name to create.
     *
     * @return mixed An object matching the $class parameter.
     */
    protected function deserialize($data, string $class)
    {
        // @todo Is this extractor required?
        $dataServiceExtractor = new DataServiceExtractor();
        $dataServiceExtractor->setClass($this->dataService->getClass());
        $dataServiceExtractor->setCustomFields($this->dataService->getCustomFields());

        // The serializer treats the $xmlns as it's own separate property, and
        // there is no way to access it from within the extractor. We can't
        // alter $context in the CJsonEncoder as it is not passed by reference.
        // @todo This feels like a bit of a hack.
        $decoded = \GuzzleHttp\Utils::jsonDecode($data, true);
        if (isset($decoded['$xmlns'])) {
            $dataServiceExtractor->setNamespaceMapping($decoded['$xmlns']);
        }

        $p = new PropertyInfoExtractor([], [$dataServiceExtractor], [], []);
        $cached = new PropertyInfoCacheExtractor($p, $this->cacheItemPool);

        $object = $this->getObjectSerializer($cached)->deserialize($data, $class, 'json');

        // Adds any missing custom field classes. A given object may not
        // contain any set fields in a given custom field namespace. In that
        // case, the serializer will never create a field class. This is fine
        // until calling code wants to check to see if a given field exists.
        // By ensuring we add in any missing custom field definitions, we
        // ensure a valid object is returned instead of null.
        if ($object instanceof ObjectInterface) {
            $customFields = $object->getCustomFields();
            $remaining = array_diff_key($this->dataService->getCustomFields(), $customFields);

            /** @var string $namespace */
            /** @var DiscoveredCustomField $field */
            foreach ($remaining as $namespace => $field) {
                $namespaceClass = $field->getClass();
                $customFields[$namespace] = new $namespaceClass();
            }

            $object->setCustomFields($customFields);
        }

        if ($object instanceof JsonInterface) {
            $object->setJson($data);
        }

        return $object;
    }

    /**
     * Load an object from mpx.
     *
     * @param UriInterface $uri     The URI to load from. This URI will always be converted to https,
     *                              making it safe to use directly from the ID of an mpx object.
     * @param array        $options (optional) An array of HTTP client options.
     *
     * @return PromiseInterface A promise to return a \Lullabot\Mpx\DataService\ObjectInterface.
     */
    public function load(UriInterface $uri, array $options = []): PromiseInterface
    {
        /** @var DataService $annotation */
        $annotation = $this->dataService->getAnnotation();

        if (!isset($options['query'])) {
            $options['query'] = [];
        }
        $options['query'] = $options['query'] += [
            'schema' => $annotation->schemaVersion,
            'form' => 'cjson',
        ];

        if ($this->authenticatedClient->hasAccount()) {
            $options['query']['account'] = (string) $this->authenticatedClient->getAccount()->getMpxId();
        }

        if ('http' == $uri->getScheme()) {
            $uri = $uri->withScheme('https');
        }

        $response = $this->authenticatedClient->requestAsync('GET', $uri, $options)->then(
            fn (ResponseInterface $response) => $this->deserialize($response->getBody(), $this->dataService->getClass())
        );

        return $response;
    }

    /**
     * Query for MPX data using with parameters.
     *
     * @param ObjectListQuery $objectListQuery (optional) The fields and values to filter by. Note these are exact
     *                                         matches.
     * @param array           $options         (optional) An array of HTTP client options.
     *
     * @return ObjectListIterator An iterator over the full result set.
     */
    public function select(?ObjectListQuery $objectListQuery = null, array $options = []): ObjectListIterator
    {
        return new ObjectListIterator($this->selectRequest($objectListQuery, $options));
    }

    /**
     * Return a promise to an object list.
     *
     * @see DataObjectFactory::select
     *
     * @param ObjectListQuery $objectListQuery (optional) The fields and values to filter by. Note these are exact
     *                                         matches.
     * @param array           $options         (optional) An array of HTTP client options.
     *
     * @return PromiseInterface A promise to return an ObjectList.
     */
    public function selectRequest(?ObjectListQuery $objectListQuery = null, array $options = []): PromiseInterface
    {
        if (!$objectListQuery) {
            $objectListQuery = new ObjectListQuery();
        }

        $annotation = $this->dataService->getAnnotation();

        if (!isset($options['query'])) {
            $options['query'] = [];
        }
        $options['query'] = $options['query'] +
            $objectListQuery->toQueryParts() + [
                'schema' => $annotation->schemaVersion,
                'form' => 'cjson',
                'count' => true,
            ];

        if ($this->authenticatedClient->hasAccount()) {
            $options['query']['account'] = (string) $this->authenticatedClient->getAccount()->getMpxId();
        }

        $uri = $this->getBaseUri($annotation, true);

        $request = $this->authenticatedClient->requestAsync('GET', $uri, $options)->then(
            fn (ResponseInterface $response) => $this->deserializeObjectList($response, $objectListQuery)
        );

        return $request;
    }

    /**
     * Deserialize an object list response.
     *
     * @param ResponseInterface $response The response to deserialize.
     * @param ObjectListQuery   $byFields The fields used to limit the response.
     *
     * @return ObjectList The deserialized list.
     */
    private function deserializeObjectList(ResponseInterface $response, ObjectListQuery $byFields): ObjectList
    {
        $data = $response->getBody();

        /** @var ObjectList $list */
        $list = $this->deserialize($data, ObjectList::class);

        // Set the json representation of each entry in the list.
        $decoded = \GuzzleHttp\Utils::jsonDecode($data, true);
        foreach ($list as $index => $item) {
            $entry = $decoded['entries'][$index];
            if (isset($decoded['$xmlns'])) {
                $entry['$xmlns'] = $decoded['$xmlns'];
            }
            $item->setJson(\GuzzleHttp\json_encode($entry));
        }
        $list->setObjectListQuery($byFields);
        $list->setDataObjectFactory($this);

        return $list;
    }

    /**
     * Get the base URI from an annotation or service registry.
     *
     * @param DataService $annotation The annotation data is being loaded for.
     * @param bool        $readonly   (optional) Load from the read-only service.
     *
     * @return string The base URI.
     */
    private function getBaseUri(DataService $annotation, bool $readonly = false): string
    {
        // Accounts are optional as you need to be able to load an account
        // before you can resolve services.
        if (!($base = $annotation->getBaseUri())) {
            // If no account is specified, we must use the ResolveAllUrls service instead.
            if (!$this->authenticatedClient->hasAccount()) {
                $resolver = new ResolveAllUrls($this->authenticatedClient, $this->cacheItemPool);

                return $resolver->resolve($annotation->getService($readonly))->getUrl().$annotation->getPath();
            }

            $resolved = $this->resolveDomain->resolve($this->authenticatedClient->getAccount());

            $base = $resolved->getUrl($annotation->getService($readonly)).$annotation->getPath();
        }

        return $base;
    }

    private function getObjectSerializer(PropertyTypeExtractorInterface $dataServiceExtractor): Serializer
    {
        // First, we need an encoder that filters out null values.
        $encoders = [new CJsonEncoder()];

        // Attempt normalizing each key in this order, including denormalizing recursively.
        $customFieldsNormalizer = new CustomFieldsNormalizer($this->dataService->getCustomFields());
        $normalizers = [
            new UnixMillisecondNormalizer(),
            new UriNormalizer(),
            $customFieldsNormalizer,
            new ObjectNormalizer(
                null, null, null,
                $dataServiceExtractor
            ),
            new ArrayDenormalizer(),
        ];

        $serializer = new Serializer($normalizers, $encoders);
        $customFieldsNormalizer->setSerializer($serializer);

        return $serializer;
    }
}
