<?php

namespace Lullabot\Mpx\DataService\Access;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\Annotation\DataService;
use Psr\Http\Message\UriInterface;

/**
 * @DataService(
 *   service="Access Data Service",
 *   baseUri="https://access.auth.theplatform.com/data/Account",
 *   schemaVersion="1.0",
 *   path="/data/Account"
 * )
 */
class Account
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
     * The description of this object.
     *
     * @var string
     */
    protected $description;

    /**
     * Whether this account is disabled.
     *
     * @var bool
     */
    protected $disabled;

    /**
     * The id field values of the Role objects that define the domains, or service instances, that this account is permitted to use.
     *
     * @var \Psr\Http\Message\UriInterface[]
     */
    protected $domainRoleIds;

    /**
     * The id field values of the Role objects that define the features, or service types, that this account is permitted to use.
     *
     * @var \Psr\Http\Message\UriInterface[]
     */
    protected $featureRoleIds;

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
     * The id of the account that owns this account.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $ownerId;

    /**
     * A public identifier for the account.
     *
     * @var string
     */
    protected $pid;

    /**
     * The account's region.
     *
     * This field is only returned by queries to the http://access.auth.theplatform.com/data/Account endpoint.
     *
     * @var string
     */
    protected $region;

    /**
     * The name of this object.
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
     * This object's modification version.
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
    public function getAddedByUserId(): UriInterface
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
     * Returns Whether this account is disabled.
     *
     * @return bool
     */
    public function getDisabled(): \boolean
    {
        return $this->disabled;
    }

    /**
     * Set Whether this account is disabled.
     *
     * @param bool
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    }

    /**
     * Returns the id field values of the Role objects that define the domains, or service instances, that this account is permitted to use.
     *
     * @return \Psr\Http\Message\UriInterface[]
     */
    public function getDomainRoleIds(): array
    {
        return $this->domainRoleIds;
    }

    /**
     * Set the id field values of the Role objects that define the domains, or service instances, that this account is permitted to use.
     *
     * @param \Psr\Http\Message\UriInterface[]
     */
    public function setDomainRoleIds($domainRoleIds)
    {
        $this->domainRoleIds = $domainRoleIds;
    }

    /**
     * Returns the id field values of the Role objects that define the features, or service types, that this account is permitted to use.
     *
     * @return \Psr\Http\Message\UriInterface[]
     */
    public function getFeatureRoleIds(): array
    {
        return $this->featureRoleIds;
    }

    /**
     * Set the id field values of the Role objects that define the features, or service types, that this account is permitted to use.
     *
     * @param \Psr\Http\Message\UriInterface[]
     */
    public function setFeatureRoleIds($featureRoleIds)
    {
        $this->featureRoleIds = $featureRoleIds;
    }

    /**
     * Returns An alternate identifier for this object that is unique within the owning account.
     *
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid;
    }

    /**
     * Set An alternate identifier for this object that is unique within the owning account.
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
    public function getId(): UriInterface
    {
        return $this->id;
    }

    /**
     * Set the globally unique URI of this object.
     *
     * @param \Psr\Http\Message\UriInterface|string
     */
    public function setId($id)
    {
        if (is_string($id)) {
            $id = new Uri($id);
        }
        $this->id = $id;
    }

    /**
     * Returns Whether this object currently allows updates.
     *
     * @return bool
     */
    public function getLocked(): \boolean
    {
        return $this->locked;
    }

    /**
     * Set Whether this object currently allows updates.
     *
     * @param bool
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    /**
     * Returns the id of the account that owns this account.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getOwnerId(): UriInterface
    {
        return $this->ownerId;
    }

    /**
     * Set the id of the account that owns this account.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
    }

    /**
     * Returns A public identifier for the account.
     *
     * @return string
     */
    public function getPid(): string
    {
        return $this->pid;
    }

    /**
     * Set A public identifier for the account.
     *
     * @param string
     */
    public function setPid($pid)
    {
        if (strlen($pid) > 64) {
            throw new \InvalidArgumentException('Public Identifiers must not be longer than 64 characters.');
        }
        if ('ASCII' != mb_check_encoding($pid)) {
            throw new \InvalidArgumentException('Public Identifiers must be ASCII encoded strings.');
        }
        $this->pid = $pid;
    }

    /**
     * Returns the account's region.
     *
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * Set the account's region.
     *
     * @param string
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * Returns the title of this object.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the title of this object.
     *
     * @param string
     */
    public function setTitle($title)
    {
        if (strlen($title) > 64) {
            throw new \InvalidArgumentException('Titles must not be longer than 64 characters.');
        }
        if (false !== strpos($title, '|')) {
            throw new \InvalidArgumentException('Titles must not contain pipe (|) characters.');
        }

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
    public function getUpdatedByUserId(): UriInterface
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
     * Returns this object's modification version.
     *
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * Set this object's modification version.
     *
     * @param int
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }
}
