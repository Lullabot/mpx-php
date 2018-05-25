<?php

namespace Lullabot\Mpx\DataService;

use Psr\Http\Message\UriInterface;

/**
 * Base class for common data used by all mpx objects.
 */
abstract class ObjectBase implements ObjectInterface, JsonInterface
{
    /**
     * The date and time that this object was created.
     *
     * @var \DateTime
     */
    protected $added;

    /**
     * The id of the user that created this object.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $addedByUserId;

    /**
     * The globally unique URI of this object.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $id;

    /**
     * The id of the account that owns this object.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $ownerId;

    /**
     * @var CustomFieldInterface[]
     */
    protected $customFields;

    /**
     * The original JSON representation of this object.
     *
     * @var array
     */
    protected $json;

    /**
     * {@inheritdoc}
     */
    public function getAdded(): \DateTime
    {
        return $this->added;
    }

    /**
     * {@inheritdoc}
     */
    public function setAdded($added)
    {
        $this->added = $added;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddedByUserId(): \Psr\Http\Message\UriInterface
    {
        return $this->addedByUserId;
    }

    /**
     * {@inheritdoc}
     */
    public function setAddedByUserId($addedByUserId)
    {
        $this->addedByUserId = $addedByUserId;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): \Psr\Http\Message\UriInterface
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId(UriInterface $id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwnerId(): \Psr\Http\Message\UriInterface
    {
        return $this->ownerId;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomFields($namespace)
    {
        return $this->customFields[$namespace];
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
        if (!$this->json) {
            throw new \LogicException('This object has no original JSON representation available');
        }

        return $this->json;
    }
}
