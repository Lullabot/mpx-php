<?php

namespace Lullabot\Mpx\DataService\Access;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\Annotation\DataService;
use Lullabot\Mpx\DataService\DateTime\NullDateTime;
use Lullabot\Mpx\DataService\ObjectBase;
use Lullabot\Mpx\DataService\PublicIdentifierInterface;
use Lullabot\Mpx\DataService\IdInterface;

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
     * The description of this object. The description field is represented as the summary field in Atom object representations to comply with that format's standards.
     *
     * @var string
     */
    protected $description;

    /**
     * Whether this account is disabled. If this field is set to true, the Access management service will not authorize any operations in this account.
     * Beginning with Access management service version 2.4, the Access management service will not authorize any operations in any subaccount of this account, regardless of the value of this field on the subaccount's Account object.
     *
     * @var bool
     */
    protected $disabled;

    /**
     * The id field values of the Role objects that define the domains, or service instances, that this account is permitted to use. The domain roles of a subaccount are gated by the domain roles assigned to its parent customer root account. If you assign a domain role to a subaccount that permits a broader scope of operations than those permitted by the parent account's domain roles, the subaccounts actual permissions are restricted to those allowed by the parent account's domain roles.Empty arrays are not represented in Atom or RSS response payloads.
     *
     * @var \Psr\Http\Message\UriInterface[]
     */
    protected $domainRoleIds = [];

    /**
     * The id field values of the Role objects that define the features, or service types, that this account is permitted to use. The feature roles of a subaccountare gated by the feature roles assigned to its parent customer root account. If you assign a feature role to a subaccount that permits a broader scope of operations than those permitted by the parent account's feature roles, the subaccounts actual permissions are restricted to those allowed by the parent account's feature roles.Empty arrays are not represented in Atom or RSS response payloads.
     *
     * @var \Psr\Http\Message\UriInterface[]
     */
    protected $featureRoleIds = [];

    /**
     * An alternate identifier for this object that is unique within the owning account. To comply with the Atom format's standards, the guid field is serialized as the <id> element in Atom payloads. If the guid field value does not begin with the value urn:publicid:, the value urn:theplatform:guid: is prepended to the actual field value. See the Retrieving Account objects page for information about retrieving Account objects based on this field's value.
     *
     * @var string
     */
    protected $guid;

    /**
     * The globally unique URI of this object. See the Retrieving Account objectspage for information about retrieving Account objects based on this field's value.Beginning with Access management service version 2.3, some data services return ownerId field values that use the host name access.auth.theplatform.com instead of the host name mps.theplatform.com. When working with these data services, URI values with the mps.theplatform.com host name are still valid values for the byOwnerId query, and for setting the account context for an operation.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $id;

    /**
     * Whether this object currently allows updates. If this field is set to true, no other fields can be updated.
     *
     * @var bool
     */
    protected $locked;

    /**
     * The id of the account that owns this account. An ownerId field value must be specified on create, unless an account context of exactly one account is set. Customer root accounts always have the ownerId value of urn:theplatform:auth:root. See Retrieving Account objects for information about retrieving Account objects based on this field's value.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $ownerId;

    /**
     * A public identifier for the account. For Account objects that represent subaccounts, this field is populated with the pid of the parent account unless you specify a different value. If you do not specify a unique value for this field in a subaccount, the inherited value is automatically updated if the owning account's pid value is modified. If you do specify a value for this field in a subaccount, you can reset the field to use the inherited value by clearing this field.
     * This field's value is not required to be unique. All of the accounts in a given account hierarchy will share a common pid value unless you explicitly set a different value for one or more of the subaccounts in that hierarchy. However, if you want to override one or more of a parent account's registry entries in a particular subaccount, you must give that subaccount a unique pid.
     * See Retrieving Account objects for information about retrieving Accountobjects based on this field's value.
     *
     * @var string
     */
    protected $pid;

    /**
     * The account's region. This field is only returned by queries to the http://access.auth.theplatform.com/data/Account endpoint.
     *
     * @var string
     */
    protected $region;

    /**
     * The name of this object. This field's value must be globally unique, and cannot include the pipe (|) character. See Retrieving Account objects for information about retrieving Account objects based on this field's value.
     *
     * @var string
     */
    protected $title;

    /**
     * The date and time this object was last modified. The data service populates this field with a current timestamp each time the object is updated. See Retrieving Account objects for information about retrieving Account objects based on this field's value.
     *
     * @var \Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface
     */
    protected $updated;

    /**
     * The id of the user that last modified this object. The data service updates this field with the id of the calling user each time the object is modified.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $updatedByUserId;

    /**
     * This object's modification version. The data service automatically increments this field's value each time the object is modified. You can use the versionfield to enforce optimistic locking.
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
     * Returns the description of this object. The description field is represented as the summary field in Atom object representations to comply with that format's standards.
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the description of this object. The description field is represented as the summary field in Atom object representations to comply with that format's standards.
     *
     * @param string $description
     */
    public function setDescription(?string $description)
    {
        $this->description = $description;
    }

    /**
     * Returns whether this account is disabled. If this field is set to true, the Access management service will not authorize any operations in this account.
     * Beginning with Access management service version 2.4, the Access management service will not authorize any operations in any subaccount of this account, regardless of the value of this field on the subaccount's Account object.
     *
     * @return bool
     */
    public function getDisabled(): ?bool
    {
        return $this->disabled;
    }

    /**
     * Set whether this account is disabled. If this field is set to true, the Access management service will not authorize any operations in this account.
     * Beginning with Access management service version 2.4, the Access management service will not authorize any operations in any subaccount of this account, regardless of the value of this field on the subaccount's Account object.
     *
     * @param bool $disabled
     */
    public function setDisabled(?bool $disabled)
    {
        $this->disabled = $disabled;
    }

    /**
     * Returns the id field values of the Role objects that define the domains, or service instances, that this account is permitted to use. The domain roles of a subaccount are gated by the domain roles assigned to its parent customer root account. If you assign a domain role to a subaccount that permits a broader scope of operations than those permitted by the parent account's domain roles, the subaccounts actual permissions are restricted to those allowed by the parent account's domain roles.Empty arrays are not represented in Atom or RSS response payloads.
     *
     * @return \Psr\Http\Message\UriInterface[]
     */
    public function getDomainRoleIds(): array
    {
        return $this->domainRoleIds;
    }

    /**
     * Set the id field values of the Role objects that define the domains, or service instances, that this account is permitted to use. The domain roles of a subaccount are gated by the domain roles assigned to its parent customer root account. If you assign a domain role to a subaccount that permits a broader scope of operations than those permitted by the parent account's domain roles, the subaccounts actual permissions are restricted to those allowed by the parent account's domain roles.Empty arrays are not represented in Atom or RSS response payloads.
     *
     * @param \Psr\Http\Message\UriInterface[] $domainRoleIds
     */
    public function setDomainRoleIds(array $domainRoleIds)
    {
        $this->domainRoleIds = $domainRoleIds;
    }

    /**
     * Returns the id field values of the Role objects that define the features, or service types, that this account is permitted to use. The feature roles of a subaccountare gated by the feature roles assigned to its parent customer root account. If you assign a feature role to a subaccount that permits a broader scope of operations than those permitted by the parent account's feature roles, the subaccounts actual permissions are restricted to those allowed by the parent account's feature roles.Empty arrays are not represented in Atom or RSS response payloads.
     *
     * @return \Psr\Http\Message\UriInterface[]
     */
    public function getFeatureRoleIds(): array
    {
        return $this->featureRoleIds;
    }

    /**
     * Set the id field values of the Role objects that define the features, or service types, that this account is permitted to use. The feature roles of a subaccountare gated by the feature roles assigned to its parent customer root account. If you assign a feature role to a subaccount that permits a broader scope of operations than those permitted by the parent account's feature roles, the subaccounts actual permissions are restricted to those allowed by the parent account's feature roles.Empty arrays are not represented in Atom or RSS response payloads.
     *
     * @param \Psr\Http\Message\UriInterface[] $featureRoleIds
     */
    public function setFeatureRoleIds(array $featureRoleIds)
    {
        $this->featureRoleIds = $featureRoleIds;
    }

    /**
     * Returns an alternate identifier for this object that is unique within the owning account. To comply with the Atom format's standards, the guid field is serialized as the <id> element in Atom payloads. If the guid field value does not begin with the value urn:publicid:, the value urn:theplatform:guid: is prepended to the actual field value. See the Retrieving Account objects page for information about retrieving Account objects based on this field's value.
     *
     * @return string
     */
    public function getGuid(): ?string
    {
        return $this->guid;
    }

    /**
     * Set an alternate identifier for this object that is unique within the owning account. To comply with the Atom format's standards, the guid field is serialized as the <id> element in Atom payloads. If the guid field value does not begin with the value urn:publicid:, the value urn:theplatform:guid: is prepended to the actual field value. See the Retrieving Account objects page for information about retrieving Account objects based on this field's value.
     *
     * @param string $guid
     */
    public function setGuid(?string $guid)
    {
        $this->guid = $guid;
    }

    /**
     * Returns the globally unique URI of this object. See the Retrieving Account objectspage for information about retrieving Account objects based on this field's value.Beginning with Access management service version 2.3, some data services return ownerId field values that use the host name access.auth.theplatform.com instead of the host name mps.theplatform.com. When working with these data services, URI values with the mps.theplatform.com host name are still valid values for the byOwnerId query, and for setting the account context for an operation.
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
     * Set the globally unique URI of this object. See the Retrieving Account objectspage for information about retrieving Account objects based on this field's value.Beginning with Access management service version 2.3, some data services return ownerId field values that use the host name access.auth.theplatform.com instead of the host name mps.theplatform.com. When working with these data services, URI values with the mps.theplatform.com host name are still valid values for the byOwnerId query, and for setting the account context for an operation.
     *
     * @param \Psr\Http\Message\UriInterface $id
     */
    public function setId(\Psr\Http\Message\UriInterface $id)
    {
        $this->id = $id;
    }

    /**
     * Returns whether this object currently allows updates. If this field is set to true, no other fields can be updated.
     *
     * @return bool
     */
    public function getLocked(): ?bool
    {
        return $this->locked;
    }

    /**
     * Set whether this object currently allows updates. If this field is set to true, no other fields can be updated.
     *
     * @param bool $locked
     */
    public function setLocked(?bool $locked)
    {
        $this->locked = $locked;
    }

    /**
     * Returns the id of the account that owns this account. An ownerId field value must be specified on create, unless an account context of exactly one account is set. Customer root accounts always have the ownerId value of urn:theplatform:auth:root. See Retrieving Account objects for information about retrieving Account objects based on this field's value.
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
     * Set the id of the account that owns this account. An ownerId field value must be specified on create, unless an account context of exactly one account is set. Customer root accounts always have the ownerId value of urn:theplatform:auth:root. See Retrieving Account objects for information about retrieving Account objects based on this field's value.
     *
     * @param \Psr\Http\Message\UriInterface $ownerId
     */
    public function setOwnerId(\Psr\Http\Message\UriInterface $ownerId)
    {
        $this->ownerId = $ownerId;
    }

    /**
     * Returns a public identifier for the account. For Account objects that represent subaccounts, this field is populated with the pid of the parent account unless you specify a different value. If you do not specify a unique value for this field in a subaccount, the inherited value is automatically updated if the owning account's pid value is modified. If you do specify a value for this field in a subaccount, you can reset the field to use the inherited value by clearing this field.
     * This field's value is not required to be unique. All of the accounts in a given account hierarchy will share a common pid value unless you explicitly set a different value for one or more of the subaccounts in that hierarchy. However, if you want to override one or more of a parent account's registry entries in a particular subaccount, you must give that subaccount a unique pid.
     * See Retrieving Account objects for information about retrieving Accountobjects based on this field's value.
     *
     * @return string
     */
    public function getPid(): ?string
    {
        return $this->pid;
    }

    /**
     * Set a public identifier for the account. For Account objects that represent subaccounts, this field is populated with the pid of the parent account unless you specify a different value. If you do not specify a unique value for this field in a subaccount, the inherited value is automatically updated if the owning account's pid value is modified. If you do specify a value for this field in a subaccount, you can reset the field to use the inherited value by clearing this field.
     * This field's value is not required to be unique. All of the accounts in a given account hierarchy will share a common pid value unless you explicitly set a different value for one or more of the subaccounts in that hierarchy. However, if you want to override one or more of a parent account's registry entries in a particular subaccount, you must give that subaccount a unique pid.
     * See Retrieving Account objects for information about retrieving Accountobjects based on this field's value.
     *
     * @param string $pid
     */
    public function setPid(?string $pid)
    {
        $this->pid = $pid;
    }

    /**
     * Returns the account's region. This field is only returned by queries to the http://access.auth.theplatform.com/data/Account endpoint.
     *
     * @return string
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * Set the account's region. This field is only returned by queries to the http://access.auth.theplatform.com/data/Account endpoint.
     *
     * @param string $region
     */
    public function setRegion(?string $region)
    {
        $this->region = $region;
    }

    /**
     * Returns the name of this object. This field's value must be globally unique, and cannot include the pipe (|) character. See Retrieving Account objects for information about retrieving Account objects based on this field's value.
     *
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the name of this object. This field's value must be globally unique, and cannot include the pipe (|) character. See Retrieving Account objects for information about retrieving Account objects based on this field's value.
     *
     * @param string $title
     */
    public function setTitle(?string $title)
    {
        $this->title = $title;
    }

    /**
     * Returns the date and time this object was last modified. The data service populates this field with a current timestamp each time the object is updated. See Retrieving Account objects for information about retrieving Account objects based on this field's value.
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
     * Set the date and time this object was last modified. The data service populates this field with a current timestamp each time the object is updated. See Retrieving Account objects for information about retrieving Account objects based on this field's value.
     *
     * @param \Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface $updated
     */
    public function setUpdated(\Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface $updated)
    {
        $this->updated = $updated;
    }

    /**
     * Returns the id of the user that last modified this object. The data service updates this field with the id of the calling user each time the object is modified.
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
     * Set the id of the user that last modified this object. The data service updates this field with the id of the calling user each time the object is modified.
     *
     * @param \Psr\Http\Message\UriInterface $updatedByUserId
     */
    public function setUpdatedByUserId(\Psr\Http\Message\UriInterface $updatedByUserId)
    {
        $this->updatedByUserId = $updatedByUserId;
    }

    /**
     * Returns this object's modification version. The data service automatically increments this field's value each time the object is modified. You can use the versionfield to enforce optimistic locking.
     *
     * @return int
     */
    public function getVersion(): ?int
    {
        return $this->version;
    }

    /**
     * Set this object's modification version. The data service automatically increments this field's value each time the object is modified. You can use the versionfield to enforce optimistic locking.
     *
     * @param int $version
     */
    public function setVersion(?int $version)
    {
        $this->version = $version;
    }
}
