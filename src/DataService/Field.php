<?php

namespace Lullabot\Mpx\DataService;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface;
use Lullabot\Mpx\DataService\DateTime\NullDateTime;
use Psr\Http\Message\UriInterface;

/**
 * Class representing custom mpx field definitions.
 *
 * @see https://docs.theplatform.com/help/wsf-field-object
 */
class Field extends ObjectBase
{
    /**
     * The date and time that this object was created
     *  This field is queryable on the following endpoints only:AssetType/Field Category/Field Media/Field MediaFile/Field Release/Field Server/Field.
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
     * The allowed values for this custom field.
     *
     * @var array
     */
    protected $allowedValues;

    /**
     * The data structure of this custom field.
     *
     * @var string
     */
    protected $dataStructure;

    /**
     * The data type of this custom field.
     *
     * @var string
     */
    protected $dataType;

    /**
     * The default value for this custom field.
     *
     * @var mixed
     */
    protected $defaultValue;

    /**
     * The description of this object.
     *
     * @var string
     */
    protected $description;

    /**
     * The node name for this custom field in XML and JSON.
     *
     * @var string
     */
    protected $fieldName;

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
     * Whether this custom field is indexed for search.
     *
     * @var bool
     */
    protected $includeInTextSearch;

    /**
     * Whether this custom field stores unique values and functions as a create key.
     *
     * @var bool
     */
    protected $isUnique;

    /**
     * The maximum string length or the number of decimal positions allowed in this custom field's value.
     *
     * @var int
     */
    protected $length;

    /**
     * Whether this object currently allows updates.
     *
     * @var bool
     */
    protected $locked;

    /**
     * The namespace this custom field belongs to.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $namespace;

    /**
     * An XML namespace prefix for this field.
     *
     * @var string
     */
    protected $namespacePrefix;

    /**
     * Whether this custom field is always available in change notifications.
     *
     * @var bool
     */
    protected $notifyAlways;

    /**
     * Whether this custom field is available on change in update notifications.
     *
     * @var bool
     */
    protected $notifyChanges;

    /**
     * Whether this custom field is available in delete notifications.
     *
     * @var bool
     */
    protected $notifyDelete;

    /**
     * The id of the account that owns this object.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $ownerId;

    /**
     * The name that this custom field is indexed under.
     *
     * @var string
     */
    protected $searchFieldName;

    /**
     * The name of this object.
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
     * This object's modification version, used for optimistic locking.
     *
     * @var int
     */
    protected $version;

    /**
     * Returns the date and time that this object was created
     *  This field is queryable on the following endpoints only:AssetType/Field Category/Field Media/Field MediaFile/Field Release/Field Server/Field.
     */
    public function getAdded(): DateTimeFormatInterface
    {
        if (!$this->added) {
            return new NullDateTime();
        }

        return $this->added;
    }

    /**
     * Set the date and time that this object was created
     *  This field is queryable on the following endpoints only:AssetType/Field Category/Field Media/Field MediaFile/Field Release/Field Server/Field.
     */
    public function setAdded(DateTimeFormatInterface $added)
    {
        $this->added = $added;
    }

    /**
     * Returns the id of the user that created this object.
     */
    public function getAddedByUserId(): UriInterface
    {
        if (!$this->addedByUserId) {
            return new Uri();
        }

        return $this->addedByUserId;
    }

    /**
     * Set the id of the user that created this object.
     */
    public function setAddedByUserId(UriInterface $addedByUserId)
    {
        $this->addedByUserId = $addedByUserId;
    }

    /**
     * Returns the allowed values for this custom field.
     */
    public function getAllowedValues(): array
    {
        return $this->allowedValues;
    }

    /**
     * Set the allowed values for this custom field.
     */
    public function setAllowedValues(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;
    }

    /**
     * Returns the data structure of this custom field.
     *
     * @return string
     */
    public function getDataStructure(): ?string
    {
        return $this->dataStructure;
    }

    /**
     * Set the data structure of this custom field.
     *
     * @param string $dataStructure
     */
    public function setDataStructure(?string $dataStructure)
    {
        $this->dataStructure = $dataStructure;
    }

    /**
     * Returns the data type of this custom field.
     *
     * @return string
     */
    public function getDataType(): ?string
    {
        return $this->dataType;
    }

    /**
     * Set the data type of this custom field.
     *
     * @param string $dataType
     */
    public function setDataType(?string $dataType)
    {
        $this->dataType = $dataType;
    }

    /**
     * Returns the default value for this custom field.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set the default value for this custom field.
     *
     * @param mixed $defaultValue
     */
    public function setDefaultValue(mixed $defaultValue)
    {
        $this->defaultValue = $defaultValue;
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
     * Returns the node name for this custom field in XML and JSON.
     *
     * @return string
     */
    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }

    /**
     * Set the node name for this custom field in XML and JSON.
     *
     * @param string $fieldName
     */
    public function setFieldName(?string $fieldName)
    {
        $this->fieldName = $fieldName;
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
     */
    public function getId(): UriInterface
    {
        if (!$this->id) {
            return new Uri();
        }

        return $this->id;
    }

    /**
     * Set the globally unique URI of this object.
     */
    public function setId(UriInterface $id)
    {
        $this->id = $id;
    }

    /**
     * Returns whether this custom field is indexed for search.
     *
     * @return bool
     */
    public function getIncludeInTextSearch(): ?bool
    {
        return $this->includeInTextSearch;
    }

    /**
     * Set whether this custom field is indexed for search.
     *
     * @param bool $includeInTextSearch
     */
    public function setIncludeInTextSearch(?bool $includeInTextSearch)
    {
        $this->includeInTextSearch = $includeInTextSearch;
    }

    /**
     * Returns whether this custom field stores unique values and functions as a create key.
     *
     * @return bool
     */
    public function getIsUnique(): ?bool
    {
        return $this->isUnique;
    }

    /**
     * Set whether this custom field stores unique values and functions as a create key.
     *
     * @param bool $isUnique
     */
    public function setIsUnique(?bool $isUnique)
    {
        $this->isUnique = $isUnique;
    }

    /**
     * Returns the maximum string length or the number of decimal positions allowed in this custom field's value.
     *
     * @return int
     */
    public function getLength(): ?int
    {
        return $this->length;
    }

    /**
     * Set the maximum string length or the number of decimal positions allowed in this custom field's value.
     *
     * @param int $length
     */
    public function setLength(?int $length)
    {
        $this->length = $length;
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
     * Returns the namespace this custom field belongs to.
     */
    public function getNamespace(): UriInterface
    {
        return $this->namespace;
    }

    /**
     * Set the namespace this custom field belongs to.
     *
     * @param UriInterface
     */
    public function setNamespace(UriInterface $namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Returns an XML namespace prefix for this field.
     *
     * @return string
     */
    public function getNamespacePrefix(): ?string
    {
        return $this->namespacePrefix;
    }

    /**
     * Set an XML namespace prefix for this field.
     *
     * @param string $namespacePrefix
     */
    public function setNamespacePrefix(?string $namespacePrefix)
    {
        $this->namespacePrefix = $namespacePrefix;
    }

    /**
     * Returns whether this custom field is always available in change notifications.
     *
     * @return bool
     */
    public function getNotifyAlways(): ?bool
    {
        return $this->notifyAlways;
    }

    /**
     * Set whether this custom field is always available in change notifications.
     *
     * @param bool $notifyAlways
     */
    public function setNotifyAlways(?bool $notifyAlways)
    {
        $this->notifyAlways = $notifyAlways;
    }

    /**
     * Returns whether this custom field is available on change in update notifications.
     *
     * @return bool
     */
    public function getNotifyChanges(): ?bool
    {
        return $this->notifyChanges;
    }

    /**
     * Set whether this custom field is available on change in update notifications.
     *
     * @param bool $notifyChanges
     */
    public function setNotifyChanges(?bool $notifyChanges)
    {
        $this->notifyChanges = $notifyChanges;
    }

    /**
     * Returns whether this custom field is available in delete notifications.
     *
     * @return bool
     */
    public function getNotifyDelete(): ?bool
    {
        return $this->notifyDelete;
    }

    /**
     * Set whether this custom field is available in delete notifications.
     *
     * @param bool $notifyDelete
     */
    public function setNotifyDelete(?bool $notifyDelete)
    {
        $this->notifyDelete = $notifyDelete;
    }

    /**
     * Returns the id of the account that owns this object.
     */
    public function getOwnerId(): UriInterface
    {
        if (!$this->ownerId) {
            return new Uri();
        }

        return $this->ownerId;
    }

    /**
     * Set the id of the account that owns this object.
     */
    public function setOwnerId(UriInterface $ownerId)
    {
        $this->ownerId = $ownerId;
    }

    /**
     * Returns the name that this custom field is indexed under.
     *
     * @return string
     */
    public function getSearchFieldName(): ?string
    {
        return $this->searchFieldName;
    }

    /**
     * Set the name that this custom field is indexed under.
     *
     * @param string $searchFieldName
     */
    public function setSearchFieldName(?string $searchFieldName)
    {
        $this->searchFieldName = $searchFieldName;
    }

    /**
     * Returns the name of this object.
     *
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the name of this object.
     *
     * @param string $title
     */
    public function setTitle(?string $title)
    {
        $this->title = $title;
    }

    /**
     * Returns the date and time this object was last modified.
     */
    public function getUpdated(): DateTimeFormatInterface
    {
        if (!$this->updated) {
            return new NullDateTime();
        }

        return $this->updated;
    }

    /**
     * Set the date and time this object was last modified.
     */
    public function setUpdated(DateTimeFormatInterface $updated)
    {
        $this->updated = $updated;
    }

    /**
     * Returns the id of the user that last modified this object.
     */
    public function getUpdatedByUserId(): UriInterface
    {
        if (!$this->updatedByUserId) {
            return new Uri();
        }

        return $this->updatedByUserId;
    }

    /**
     * Set the id of the user that last modified this object.
     */
    public function setUpdatedByUserId(UriInterface $updatedByUserId)
    {
        $this->updatedByUserId = $updatedByUserId;
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
