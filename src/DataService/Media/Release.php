<?php

namespace Lullabot\Mpx\DataService\Media;

/**
 * @\Lullabot\Mpx\DataService\Annotation\DataService(
 *   service="Media Data Service",
 *   schemaVersion="1.10",
 *   objectType="Release",
 * )
 */
class Release
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
     * The id of the AdPolicy object this object is associated with.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $adPolicyId;

    /**
     * Whether this object is approved; if false this object is not visible in feeds.
     *
     * @var bool
     */
    protected $approved;

    /**
     * The delivery method for this object.
     *
     * @var string
     */
    protected $delivery;

    /**
     * The description of this object.
     *
     * @var string
     */
    protected $description;

    /**
     * The id of the MediaFile object this object is associated with.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $fileId;

    /**
     * An alternate identifier for this object that is unique within the owning account.
     *
     * @var string
     */
    protected $guid;

    /**
     * The globally unique URI of this object.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $id;

    /**
     * Whether this object currently allows updates.
     *
     * @var bool
     */
    protected $locked;

    /**
     * The id of the Media object this object is associated with.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $mediaId;

    /**
     * The id of the account that owns this object.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $ownerId;

    /**
     * Optional release URL parameters.
     *
     * @var string
     */
    protected $parameters;

    /**
     * The globally unique public identifier for this object.
     *
     * @var string
     */
    protected $pid;

    /**
     * The id of the Restriction object this object is associated with.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $restrictionId;

    /**
     * The generic name of this object.
     *
     * @var string
     */
    protected $title;

    /**
     * The date and time this object was last modified.
     *
     * @var \DateTime
     */
    protected $updated;

    /**
     * The id of the user that last modified this object.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $updatedByUserId;

    /**
     * The public URL for this object.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $url;

    /**
     * This object's modification version, used for optimistic locking.
     *
     * @var int
     */
    protected $version;

    /**
     * Returns the date and time that this object was created.
     *
     * @return \DateTime
     */
    public function getAdded(): \DateTime
    {
        return $this->added;
    }

    /**
     * Set the date and time that this object was created.
     *
     * @param \DateTime
     */
    public function setAdded($added)
    {
        $this->added = $added;
    }

    /**
     * Returns the id of the user that created this object.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getAddedByUserId(): \Psr\Http\Message\UriInterface
    {
        return $this->addedByUserId;
    }

    /**
     * Set the id of the user that created this object.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setAddedByUserId($addedByUserId)
    {
        $this->addedByUserId = $addedByUserId;
    }

    /**
     * Returns the id of the AdPolicy object this object is associated with.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getAdPolicyId(): \Psr\Http\Message\UriInterface
    {
        return $this->adPolicyId;
    }

    /**
     * Set the id of the AdPolicy object this object is associated with.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setAdPolicyId($adPolicyId)
    {
        $this->adPolicyId = $adPolicyId;
    }

    /**
     * Returns whether this object is approved; if false this object is not visible in feeds.
     *
     * @return bool
     */
    public function getApproved(): bool
    {
        return $this->approved;
    }

    /**
     * Set whether this object is approved; if false this object is not visible in feeds.
     *
     * @param bool
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }

    /**
     * Returns the delivery method for this object.
     *
     * @return string
     */
    public function getDelivery(): string
    {
        return $this->delivery;
    }

    /**
     * Set the delivery method for this object.
     *
     * @param string
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;
    }

    /**
     * Returns the description of this object.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the description of this object.
     *
     * @param string
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns the id of the MediaFile object this object is associated with.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getFileId(): \Psr\Http\Message\UriInterface
    {
        return $this->fileId;
    }

    /**
     * Set the id of the MediaFile object this object is associated with.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setFileId($fileId)
    {
        $this->fileId = $fileId;
    }

    /**
     * Returns an alternate identifier for this object that is unique within the owning account.
     *
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid;
    }

    /**
     * Set an alternate identifier for this object that is unique within the owning account.
     *
     * @param string
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;
    }

    /**
     * Returns the globally unique URI of this object.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getId(): \Psr\Http\Message\UriInterface
    {
        return $this->id;
    }

    /**
     * Set the globally unique URI of this object.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns whether this object currently allows updates.
     *
     * @return bool
     */
    public function getLocked(): bool
    {
        return $this->locked;
    }

    /**
     * Set whether this object currently allows updates.
     *
     * @param bool
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    /**
     * Returns the id of the Media object this object is associated with.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getMediaId(): \Psr\Http\Message\UriInterface
    {
        return $this->mediaId;
    }

    /**
     * Set the id of the Media object this object is associated with.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setMediaId($mediaId)
    {
        $this->mediaId = $mediaId;
    }

    /**
     * Returns the id of the account that owns this object.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getOwnerId(): \Psr\Http\Message\UriInterface
    {
        return $this->ownerId;
    }

    /**
     * Set the id of the account that owns this object.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
    }

    /**
     * Returns optional release URL parameters.
     *
     * @return string
     */
    public function getParameters(): string
    {
        return $this->parameters;
    }

    /**
     * Set optional release URL parameters.
     *
     * @param string
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns the globally unique public identifier for this object.
     *
     * @return string
     */
    public function getPid(): string
    {
        return $this->pid;
    }

    /**
     * Set the globally unique public identifier for this object.
     *
     * @param string
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    /**
     * Returns the id of the Restriction object this object is associated with.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getRestrictionId(): \Psr\Http\Message\UriInterface
    {
        return $this->restrictionId;
    }

    /**
     * Set the id of the Restriction object this object is associated with.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setRestrictionId($restrictionId)
    {
        $this->restrictionId = $restrictionId;
    }

    /**
     * Returns the generic name of this object.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the generic name of this object.
     *
     * @param string
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the date and time this object was last modified.
     *
     * @return \DateTime
     */
    public function getUpdated(): \DateTime
    {
        return $this->updated;
    }

    /**
     * Set the date and time this object was last modified.
     *
     * @param \DateTime
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * Returns the id of the user that last modified this object.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getUpdatedByUserId(): \Psr\Http\Message\UriInterface
    {
        return $this->updatedByUserId;
    }

    /**
     * Set the id of the user that last modified this object.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setUpdatedByUserId($updatedByUserId)
    {
        $this->updatedByUserId = $updatedByUserId;
    }

    /**
     * Returns the public URL for this object.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getUrl(): \Psr\Http\Message\UriInterface
    {
        return $this->url;
    }

    /**
     * Set the public URL for this object.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Returns this object's modification version, used for optimistic locking.
     *
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * Set this object's modification version, used for optimistic locking.
     *
     * @param int
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }
}
