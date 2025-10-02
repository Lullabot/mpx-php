<?php

namespace Lullabot\Mpx\DataService;

use Psr\Http\Message\UriInterface;

/**
 * Base class for common data used by all mpx objects.
 */
abstract class ObjectBase implements ObjectInterface
{
    /**
     * @var \Lullabot\Mpx\DataService\CustomFieldInterface[]
     */
    protected $customFields = [];

    /**
     * The original JSON representation of this object.
     *
     * @var array
     */
    protected $json;

    public function getCustomFields()
    {
        return $this->customFields;
    }

    public function setCustomFields(array $customFields)
    {
        $this->customFields = $customFields;
    }

    public function setJson(string $json)
    {
        $this->json = \GuzzleHttp\Utils::jsonDecode($json, true);
    }

    public function getJson()
    {
        if (null === $this->json) {
            throw new \LogicException('This object has no original JSON representation available');
        }

        return $this->json;
    }

    public function getMpxId(): UriInterface
    {
        return $this->getId();
    }

    public function setMpxId(UriInterface $id)
    {
        $this->setId($id);
    }
}
