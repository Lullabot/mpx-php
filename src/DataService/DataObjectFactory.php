<?php

namespace Lullabot\Mpx\DataService;

use Lullabot\Mpx\DataService\Access\Account;
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
     * @todo Add a load that takes a full URL?
     *
     * @param int                                      $id      The numeric ID to load.
     * @param \Lullabot\Mpx\DataService\Access\Account $account
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function load(int $id, Account $account = null)
    {
        /** @var \Lullabot\Mpx\DataService\Annotation\DataService $annotation */
        $annotation = $this->description['annotation'];

        // Accounts are optional as you need to be able to load an account
        // before you can resolve services.
        // @todo Can we do this by calling ResolveAllUrls?
        if (!($base = $annotation->getBaseUri())) {
            $resolved = $this->resolveDomain->resolve($account);
            $base = $resolved->getUrl($annotation->getService()).$annotation->getPath();
        }
        $uri = $base.'/'.$id;

        $options = [
            'query' => [
                'schema' => $annotation->getSchemaVersion(),
                'form' => 'cjson',
            ],
        ];

        $response = $this->userSession->requestAsync('GET', $uri, $options)->then(function (ResponseInterface $response) {
            return $this->deserialize($this->description['class'], $response->getBody());
        });

        return $response;
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
}
