<?php

namespace Lullabot\Mpx\DataService;

use Psr\Http\Message\UriInterface;

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
     * @var UriInterface
     */
    protected $addedByUserId;

    /**
     * The globally unique URI of this object.
     *
     * @var UriInterface
     */
    protected $id;

    /**
     * The id of the account that owns this object.
     *
     * @var UriInterface
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
     * @return UriInterface
     */
    public function getAddedByUserId(): UriInterface
    {
        return $this->addedByUserId;
    }

    /**
     * Set The id of the user that created this object.
     *
     * @param UriInterface
     */
    public function setAddedByUserId($addedByUserId)
    {
        $this->addedByUserId = $addedByUserId;
    }

    /**
     * Returns The globally unique URI of this object.
     *
     * @return UriInterface
     */
    public function getId(): UriInterface
    {
        return $this->id;
    }

    /**
     * Set The globally unique URI of this object.
     *
     * @param UriInterface
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns The id of the account that owns this object.
     *
     * @return UriInterface
     */
    public function getOwnerId(): UriInterface
    {
        return $this->ownerId;
    }

    /**
     * Set The id of the account that owns this object.
     *
     * @param UriInterface
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
    }
}
