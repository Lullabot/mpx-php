<?php

namespace Lullabot\Mpx\DataService;

use Psr\Http\Message\UriInterface;

/**
 * Trait to handle common properties to data across different services.
 */
trait BaseDataTrait
{
    use ConversionTrait;

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
     * the identifier for the advertising policy for this object.
     *
     * @var UriInterface
     */
    protected $adPolicyId;

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
     * @param \DateTime|int
     */
    public function setAdded($added)
    {
        $this->added = $this->convertDateTime($added);
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
     * @param UriInterface|string
     */
    public function setAddedByUserId($addedByUserId)
    {
        $this->addedByUserId = $this->convertUri($addedByUserId);
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
        $this->id = $this->convertUri($id);
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
        $this->ownerId = $this->convertUri($ownerId);
    }
}
