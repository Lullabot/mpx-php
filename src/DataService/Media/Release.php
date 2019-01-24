<?php

namespace Lullabot\Mpx\DataService\Media;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\Annotation\DataService;
use Lullabot\Mpx\DataService\DateTime\NullDateTime;
use Lullabot\Mpx\DataService\ObjectBase;
use Lullabot\Mpx\DataService\PublicIdWithGuidInterface;

/**
 * @DataService(
 *   service="Media Data Service",
 *   schemaVersion="1.10",
 *   objectType="Release",
 * )
 */
class Release extends ObjectBase implements PublicIdWithGuidInterface
{
    /**
     * The date and time that this object was created.
     *
     * @var \Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface
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
     * @var \Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface
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
     * @return \Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface
     */
    public function getAdded(): \Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface
    {
        if (!$this->added) {
            return new NullDateTime();
        }

        return $this->added;
    }

    /**
     * Set the date and time that this object was created.
     *
     * @param \Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface $added
     */
    public function setAdded(\Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface $added)
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
        if (!$this->addedByUserId) {
            return new Uri();
        }

        return $this->addedByUserId;
    }

    /**
     * Set the id of the user that created this object.
     *
     * @param \Psr\Http\Message\UriInterface $addedByUserId
     */
    public function setAddedByUserId(\Psr\Http\Message\UriInterface $addedByUserId)
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
        if (!$this->adPolicyId) {
            return new Uri();
        }

        return $this->adPolicyId;
    }

    /**
     * Set the id of the AdPolicy object this object is associated with.
     *
     * @param \Psr\Http\Message\UriInterface $adPolicyId
     */
    public function setAdPolicyId(\Psr\Http\Message\UriInterface $adPolicyId)
    {
        $this->adPolicyId = $adPolicyId;
    }

    /**
     * Returns whether this object is approved; if false this object is not visible in feeds.
     *
     * @return bool
     */
    public function getApproved(): ?bool
    {
        return $this->approved;
    }

    /**
     * Set whether this object is approved; if false this object is not visible in feeds.
     *
     * @param bool $approved
     */
    public function setApproved(?bool $approved)
    {
        $this->approved = $approved;
    }

    /**
     * Returns the delivery method for this object.
     *
     * @return string
     */
    public function getDelivery(): ?string
    {
        return $this->delivery;
    }

    /**
     * Set the delivery method for this object.
     *
     * @param string $delivery
     */
    public function setDelivery(?string $delivery)
    {
        $this->delivery = $delivery;
    }

    /**
     * Returns the description of this object.
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the description of this object.
     *
     * @param string $description
     */
    public function setDescription(?string $description)
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
        if (!$this->fileId) {
            return new Uri();
        }

        return $this->fileId;
    }

    /**
     * Set the id of the MediaFile object this object is associated with.
     *
     * @param \Psr\Http\Message\UriInterface $fileId
     */
    public function setFileId(\Psr\Http\Message\UriInterface $fileId)
    {
        $this->fileId = $fileId;
    }

    /**
     * Returns an alternate identifier for this object that is unique within the owning account.
     *
     * @return string
     */
    public function getGuid(): ?string
    {
        return $this->guid;
    }

    /**
     * Set an alternate identifier for this object that is unique within the owning account.
     *
     * @param string $guid
     */
    public function setGuid(?string $guid)
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
        if (!$this->id) {
            return new Uri();
        }

        return $this->id;
    }

    /**
     * Set the globally unique URI of this object.
     *
     * @param \Psr\Http\Message\UriInterface $id
     */
    public function setId(\Psr\Http\Message\UriInterface $id)
    {
        $this->id = $id;
    }

    /**
     * Returns whether this object currently allows updates.
     *
     * @return bool
     */
    public function getLocked(): ?bool
    {
        return $this->locked;
    }

    /**
     * Set whether this object currently allows updates.
     *
     * @param bool $locked
     */
    public function setLocked(?bool $locked)
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
        if (!$this->mediaId) {
            return new Uri();
        }

        return $this->mediaId;
    }

    /**
     * Set the id of the Media object this object is associated with.
     *
     * @param \Psr\Http\Message\UriInterface $mediaId
     */
    public function setMediaId(\Psr\Http\Message\UriInterface $mediaId)
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
        if (!$this->ownerId) {
            return new Uri();
        }

        return $this->ownerId;
    }

    /**
     * Set the id of the account that owns this object.
     *
     * @param \Psr\Http\Message\UriInterface $ownerId
     */
    public function setOwnerId(\Psr\Http\Message\UriInterface $ownerId)
    {
        $this->ownerId = $ownerId;
    }

    /**
     * Returns optional release URL parameters.
     *
     * @return string
     */
    public function getParameters(): ?string
    {
        return $this->parameters;
    }

    /**
     * Set optional release URL parameters.
     *
     * @param string $parameters
     */
    public function setParameters(?string $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns the globally unique public identifier for this object.
     *
     * @return string
     */
    public function getPid(): ?string
    {
        return $this->pid;
    }

    /**
     * Set the globally unique public identifier for this object.
     *
     * @param string $pid
     */
    public function setPid(?string $pid)
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
        if (!$this->restrictionId) {
            return new Uri();
        }

        return $this->restrictionId;
    }

    /**
     * Set the id of the Restriction object this object is associated with.
     *
     * @param \Psr\Http\Message\UriInterface $restrictionId
     */
    public function setRestrictionId(\Psr\Http\Message\UriInterface $restrictionId)
    {
        $this->restrictionId = $restrictionId;
    }

    /**
     * Returns the generic name of this object.
     *
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the generic name of this object.
     *
     * @param string $title
     */
    public function setTitle(?string $title)
    {
        $this->title = $title;
    }

    /**
     * Returns the date and time this object was last modified.
     *
     * @return \Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface
     */
    public function getUpdated(): \Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface
    {
        if (!$this->updated) {
            return new NullDateTime();
        }

        return $this->updated;
    }

    /**
     * Set the date and time this object was last modified.
     *
     * @param \Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface $updated
     */
    public function setUpdated(\Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface $updated)
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
        if (!$this->updatedByUserId) {
            return new Uri();
        }

        return $this->updatedByUserId;
    }

    /**
     * Set the id of the user that last modified this object.
     *
     * @param \Psr\Http\Message\UriInterface $updatedByUserId
     */
    public function setUpdatedByUserId(\Psr\Http\Message\UriInterface $updatedByUserId)
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
        if (!$this->url) {
            return new Uri();
        }

        return $this->url;
    }

    /**
     * Set the public URL for this object.
     *
     * @param \Psr\Http\Message\UriInterface $url
     */
    public function setUrl(\Psr\Http\Message\UriInterface $url)
    {
        $this->url = $url;
    }

    /**
     * Returns this object's modification version, used for optimistic locking.
     *
     * @return int
     */
    public function getVersion(): ?int
    {
        return $this->version;
    }

    /**
     * Set this object's modification version, used for optimistic locking.
     *
     * @param int $version
     */
    public function setVersion(?int $version)
    {
        $this->version = $version;
    }
}
