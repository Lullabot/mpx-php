<?php

namespace Lullabot\Mpx\DataService;

use Psr\Http\Message\UriInterface;

/**
 * Base class for common data used by all mpx objects.
 */
abstract class ObjectBase implements ObjectInterface
{
    /**
     * @var CustomFieldInterface[]
     */
    protected $customFields = [];

    /**
     * The original JSON representation of this object.
     *
     * @var array
     */
    protected $json;

    /**
     * {@inheritdoc}
     */
    public function getCustomFields()
    {
        return $this->customFields;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomFields(array $customFields)
    {
        $this->customFields = $customFields;
    }

    /**
     * {@inheritdoc}
     */
    public function setJson(string $json)
    {
        $this->json = \GuzzleHttp\json_decode($json, true);
    }

    /**
     * {@inheritdoc}
     */
    public function getJson()
    {
        if (null === $this->json) {
            throw new \LogicException('This object has no original JSON representation available');
        }

        return $this->json;
    }

    /**
     * {@inheritdoc}
     */
    public function getMpxId(): UriInterface
    {
        return $this->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function setMpxId(UriInterface $id)
    {
        $this->setId($id);
    }
}
