<?php

namespace Lullabot\Mpx\DataService\Access;

use Lullabot\Mpx\DataService\Annotation\DataService;
use Lullabot\Mpx\DataService\ObjectBase;
use Lullabot\Mpx\DataService\PublicIdentifierInterface;
use Lullabot\Mpx\DataService\PublicIdentifierTrait;
use Lullabot\Mpx\DataService\IdInterface;
use Psr\Http\Message\UriInterface;

/**
 * @DataService(
 *   service="Access Data Service",
 *   baseUri="https://access.auth.theplatform.com/data/Account",
 *   schemaVersion="1.0",
 *   objectType="Account"
 * )
 */
class Account extends ObjectBase implements PublicIdentifierInterface, IdInterface
{
    use PublicIdentifierTrait;

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
     * Whether this object currently allows updates.
     *
     * @var bool
     */
    protected $locked;

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
     * Returns whether this account is disabled.
     *
     * @return bool
     */
    public function getDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * Set whether this account is disabled.
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
     * Returns Whether this object currently allows updates.
     *
     * @return bool
     */
    public function getLocked(): bool
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
