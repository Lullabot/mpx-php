<?php

namespace Lullabot\Mpx\DataService;

/**
 * Trait to handle common properties to data across different services.
 */
trait BaseDataTrait
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
     * the identifier for the advertising policy for this object.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $adPolicyId;

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
     * Returns The date and time that this object was created.
     *
     * @return \DateTime
     */
    public function getAdded(): \DateTime
    {
        return $this->added;
    }

    /**
     * Set The date and time that this object was created.
     *
     * @param \DateTime
     */
    public function setAdded($added)
    {
        $this->added = $added;
    }

    /**
     * Returns The id of the user that created this object.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getAddedByUserId(): \Psr\Http\Message\UriInterface
    {
        return $this->addedByUserId;
    }

    /**
     * Set The id of the user that created this object.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setAddedByUserId($addedByUserId)
    {
        $this->addedByUserId = $addedByUserId;
    }

    /**
     * Returns The globally unique URI of this object.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getId(): \Psr\Http\Message\UriInterface
    {
        return $this->id;
    }

    /**
     * Set The globally unique URI of this object.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns The id of the account that owns this object.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getOwnerId(): \Psr\Http\Message\UriInterface
    {
        return $this->ownerId;
    }

    /**
     * Set The id of the account that owns this object.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
    }
}
