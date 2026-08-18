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

    public function setCustomFields(array $customFields): void
    {
        $this->customFields = $customFields;
    }

    public function setJson(string $json): void
    {
        $this->json = json_decode($json, true, 512, \JSON_THROW_ON_ERROR);
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

    public function setMpxId(UriInterface $id): void
    {
        $this->setId($id);
    }
}
