<?php

namespace Lullabot\Mpx\DataService\Feeds;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\Annotation\DataService;
use Lullabot\Mpx\DataService\DateTime\NullDateTime;
use Lullabot\Mpx\DataService\ObjectBase;
use Lullabot\Mpx\DataService\PublicIdentifierInterface;

/**
 * FeedConfig represents the configuration of a feed.
 *
 * @see https://docs.theplatform.com/help/feeds-feedconfig-endpoint
 *
 * @DataService(
 *     service="Feeds Data Service",
 *     objectType="FeedConfig",
 *     schemaVersion="2.2",
 * )
 */
class FeedConfig extends ObjectBase implements PublicIdentifierInterface
{
    /**
     * The parameters that are passed to a custom feed adapter for processing at runtime.
     *
     * @var string
     */
    protected $adapterParameters;

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
     * The list of administrative workflow tags for the feed configuration.
     *
     * @var string[]
     */
    protected $adminTags = [];

    /**
     * The id of the AdPolicythat is applied to the feed.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $adPolicyId;

    /**
     * The author of the feed content.
     *
     * @var string
     */
    protected $author;

    /**
     * The list of object fields that can be retrieved in a feed request. Feed clients can only request fields that are listed in this field.
     *
     * @var string[]
     */
    protected $availableFields = [];

    /**
     * A filter applied to any items specified in the pinnedIds field and any items selected by the segmentQueriesfield.
     *
     * @var string
     */
    protected $baseQuery;

    /**
     * The minimum time that items in the cache are valid.
     *
     * @var float
     */
    protected $cacheLifetime;

    /**
     * The response behavior during a cache refresh.
     *
     * @var string
     */
    protected $cacheRefreshStrategy;

    /**
     * The identifier for the default thumbnail for each item in the feed.
     *
     * @var string
     */
    protected $defaultThumbnailAssetType;

    /**
     * The description of the feed.
     *
     * @var string
     */
    protected $description;

    /**
     * Whether the feed is disabled.
     *
     * @var bool
     */
    protected $disabled;

    /**
     * The maximum number of items in the feed.
     *
     * @var int
     */
    protected $endIndex;

    /**
     * The feed items' object type.
     *
     * @var string
     */
    protected $feedType;

    /**
     * The default format of the feed.
     *
     * @var string
     */
    protected $form;

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
     * The URL template that is used to create the value of each feed item's link field.
     *
     * @var string
     */
    protected $itemLinkUrl;

    /**
     * Whether to only include items that are currently available.
     *
     * @var bool
     */
    protected $limitByAvailableDate;

    /**
     * Whether to only include items that are approved.
     *
     * @var bool
     */
    protected $limitToApproved;

    /**
     * The URL value of the feed's link field.
     *
     * @var string
     */
    protected $linkUrl;

    /**
     * Whether this object currently allows updates.
     *
     * @var bool
     */
    protected $locked;

    /**
     * The id of the account that owns this object.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $ownerId;

    /**
     * The globally-unique public identifier that is used to request this feed.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $pid;

    /**
     * The IDs of specific items to include in the feed. These items are in addition to any selected by the segmentQueries field, and may be filtered by the baseQuery field.
     *
     * @var int[]
     */
    protected $pinnedIds = [];

    /**
     * The URL template that is used to create the value of each feed item's content.player.urlfield.
     *
     * @var string
     */
    protected $playerUrl;

    /**
     * Whether to use the sortKeys to sort items in the feed when a Q query is included in the FeedConfig or the feed request.
     *
     * @var bool
     */
    protected $preferSortKeysOnSearch;

    /**
     * The type of query engine used to retrieve the feed items.
     *
     * @var string
     */
    protected $queryEngine;

    /**
     * The list of key-value URL parameters that are appended to the feed item's content.urlfield.
     *
     * @var string
     */
    protected $releaseUrlParameters;

    /**
     * The id of the Restriction that is applied to the feed.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $restrictionId;

    /**
     * The object schema version of the feed items.
     *
     * @var string
     */
    protected $schema;

    /**
     * A query used to select items for the feed. These items are in addition to any specified in the pinnedIds field, and may be filtered by the baseQuery field.
     *
     * @var string[]
     */
    protected $segmentQueries = [];

    /**
     * The list of fields and directions used to sort items in the feed.
     *
     * @var SortKey[]
     */
    protected $sortKeys = [];

    /**
     * The sub feeds of the feed. Sub feeds contain all items of the specified SubFeed.feedType in the feed owner's account. Currently, only a Category sub feed of a Media feed is supported.
     *
     * @var SubFeed[]
     */
    protected $subFeeds = [];

    /**
     * The query or key names used to select the thumbnail files for each item in the feed.
     *
     * @var string
     */
    protected $thumbnailFilter;

    /**
     * The title of the feed.
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
     * The type of URL that is returned in each feed item's content.urlfield.
     *
     * @var string
     */
    protected $urlType;

    /**
     * Whether to include the feed-level elements and any feed-level custom fields in the feed header.
     *
     * @var bool
     */
    protected $validFeed;

    /**
     * This object's modification version, used for optimistic locking.
     *
     * @var int
     */
    protected $version;

    /**
     * Returns the parameters that are passed to a custom feed adapter for processing at runtime.
     *
     * @return string
     */
    public function getAdapterParameters(): ?string
    {
        return $this->adapterParameters;
    }

    /**
     * Set the parameters that are passed to a custom feed adapter for processing at runtime.
     *
     * @param string $adapterParameters
     */
    public function setAdapterParameters(?string $adapterParameters)
    {
        $this->adapterParameters = $adapterParameters;
    }

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
     * Returns the list of administrative workflow tags for the feed configuration.
     *
     * @return string[]
     */
    public function getAdminTags(): array
    {
        return $this->adminTags;
    }

    /**
     * Set the list of administrative workflow tags for the feed configuration.
     *
     * @param string[] $adminTags
     */
    public function setAdminTags(array $adminTags)
    {
        $this->adminTags = $adminTags;
    }

    /**
     * Returns the id of the AdPolicy that is applied to the feed.
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
     * Set the id of the AdPolicy that is applied to the feed.
     *
     * @param \Psr\Http\Message\UriInterface $adPolicyId
     */
    public function setAdPolicyId(\Psr\Http\Message\UriInterface $adPolicyId)
    {
        $this->adPolicyId = $adPolicyId;
    }

    /**
     * Returns the author of the feed content.
     *
     * @return string
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * Set the author of the feed content.
     *
     * @param string $author
     */
    public function setAuthor(?string $author)
    {
        $this->author = $author;
    }

    /**
     * Returns the list of object fields that can be retrieved in a feed request. Feed clients can only request fields that are listed in this field.
     *
     * @return string[]
     */
    public function getAvailableFields(): array
    {
        return $this->availableFields;
    }

    /**
     * Set the list of object fields that can be retrieved in a feed request. Feed clients can only request fields that are listed in this field.
     *
     * @param string[] $availableFields
     */
    public function setAvailableFields(array $availableFields)
    {
        $this->availableFields = $availableFields;
    }

    /**
     * Returns a filter applied to any items specified in the pinnedIds field and any items selected by the segmentQueriesfield.
     *
     * @return string
     */
    public function getBaseQuery(): ?string
    {
        return $this->baseQuery;
    }

    /**
     * Set a filter applied to any items specified in the pinnedIds field and any items selected by the segmentQueriesfield.
     *
     * @param string $baseQuery
     */
    public function setBaseQuery(?string $baseQuery)
    {
        $this->baseQuery = $baseQuery;
    }

    /**
     * Returns the minimum time that items in the cache are valid.
     *
     * @return float
     */
    public function getCacheLifetime(): ?float
    {
        return $this->cacheLifetime;
    }

    /**
     * Set the minimum time that items in the cache are valid.
     *
     * @param float $cacheLifetime
     */
    public function setCacheLifetime(?float $cacheLifetime)
    {
        $this->cacheLifetime = $cacheLifetime;
    }

    /**
     * Returns the response behavior during a cache refresh.
     *
     * @return string
     */
    public function getCacheRefreshStrategy(): ?string
    {
        return $this->cacheRefreshStrategy;
    }

    /**
     * Set the response behavior during a cache refresh.
     *
     * @param string $cacheRefreshStrategy
     */
    public function setCacheRefreshStrategy(?string $cacheRefreshStrategy)
    {
        $this->cacheRefreshStrategy = $cacheRefreshStrategy;
    }

    /**
     * Returns the identifier for the default thumbnail for each item in the feed.
     *
     * @return string
     */
    public function getDefaultThumbnailAssetType(): ?string
    {
        return $this->defaultThumbnailAssetType;
    }

    /**
     * Set the identifier for the default thumbnail for each item in the feed.
     *
     * @param string $defaultThumbnailAssetType
     */
    public function setDefaultThumbnailAssetType(?string $defaultThumbnailAssetType)
    {
        $this->defaultThumbnailAssetType = $defaultThumbnailAssetType;
    }

    /**
     * Returns the description of the feed.
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the description of the feed.
     *
     * @param string $description
     */
    public function setDescription(?string $description)
    {
        $this->description = $description;
    }

    /**
     * Returns whether the feed is disabled.
     *
     * @return bool
     */
    public function getDisabled(): ?bool
    {
        return $this->disabled;
    }

    /**
     * Set whether the feed is disabled.
     *
     * @param bool $disabled
     */
    public function setDisabled(?bool $disabled)
    {
        $this->disabled = $disabled;
    }

    /**
     * Returns the maximum number of items in the feed.
     *
     * @return int
     */
    public function getEndIndex(): ?int
    {
        return $this->endIndex;
    }

    /**
     * Set the maximum number of items in the feed.
     *
     * @param int $endIndex
     */
    public function setEndIndex(?int $endIndex)
    {
        $this->endIndex = $endIndex;
    }

    /**
     * Returns the feed items' object type.
     *
     * @return string
     */
    public function getFeedType(): ?string
    {
        return $this->feedType;
    }

    /**
     * Set the feed items' object type.
     *
     * @param string $feedType
     */
    public function setFeedType(?string $feedType)
    {
        $this->feedType = $feedType;
    }

    /**
     * Returns the default format of the feed.
     *
     * @return string
     */
    public function getForm(): ?string
    {
        return $this->form;
    }

    /**
     * Set the default format of the feed.
     *
     * @param string $form
     */
    public function setForm(?string $form)
    {
        $this->form = $form;
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
     * Returns the URL template that is used to create the value of each feed item's link field.
     *
     * @return string
     */
    public function getItemLinkUrl(): ?string
    {
        return $this->itemLinkUrl;
    }

    /**
     * Set the URL template that is used to create the value of each feed item's link field.
     *
     * @param string $itemLinkUrl
     */
    public function setItemLinkUrl(?string $itemLinkUrl)
    {
        $this->itemLinkUrl = $itemLinkUrl;
    }

    /**
     * Returns whether to only include items that are currently available.
     *
     * @return bool
     */
    public function getLimitByAvailableDate(): ?bool
    {
        return $this->limitByAvailableDate;
    }

    /**
     * Set whether to only include items that are currently available.
     *
     * @param bool $limitByAvailableDate
     */
    public function setLimitByAvailableDate(?bool $limitByAvailableDate)
    {
        $this->limitByAvailableDate = $limitByAvailableDate;
    }

    /**
     * Returns whether to only include items that are approved.
     *
     * @return bool
     */
    public function getLimitToApproved(): ?bool
    {
        return $this->limitToApproved;
    }

    /**
     * Set whether to only include items that are approved.
     *
     * @param bool $limitToApproved
     */
    public function setLimitToApproved(?bool $limitToApproved)
    {
        $this->limitToApproved = $limitToApproved;
    }

    /**
     * Returns the URL value of the feed's link field.
     *
     * @return string
     */
    public function getLinkUrl(): ?string
    {
        return $this->linkUrl;
    }

    /**
     * Set the URL value of the feed's link field.
     *
     * @param string $linkUrl
     */
    public function setLinkUrl(?string $linkUrl)
    {
        $this->linkUrl = $linkUrl;
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
     * Returns the globally-unique public identifier that is used to request this feed.
     *
     * @return string
     */
    public function getPid(): ?string
    {
        return $this->pid;
    }

    /**
     * Set the globally-unique public identifier that is used to request this feed.
     *
     * @param string $pid
     */
    public function setPid(?string $pid)
    {
        $this->pid = $pid;
    }

    /**
     * Returns the IDs of specific items to include in the feed. These items are in addition to any selected by the segmentQueries field, and may be filtered by the baseQuery field.
     *
     * @return int[]
     */
    public function getPinnedIds(): array
    {
        return $this->pinnedIds;
    }

    /**
     * Set the IDs of specific items to include in the feed. These items are in addition to any selected by the segmentQueries field, and may be filtered by the baseQuery field.
     *
     * @param int[] $pinnedIds
     */
    public function setPinnedIds(array $pinnedIds)
    {
        $this->pinnedIds = $pinnedIds;
    }

    /**
     * Returns the URL template that is used to create the value of each feed item's content.player.urlfield.
     *
     * @return string
     */
    public function getPlayerUrl(): ?string
    {
        return $this->playerUrl;
    }

    /**
     * Set the URL template that is used to create the value of each feed item's content.player.urlfield.
     *
     * @param string $playerUrl
     */
    public function setPlayerUrl(?string $playerUrl)
    {
        $this->playerUrl = $playerUrl;
    }

    /**
     * Returns whether to use the sortKeys to sort items in the feed when a Q query is included in the FeedConfig or the feed request.
     *
     * @return bool
     */
    public function getPreferSortKeysOnSearch(): ?bool
    {
        return $this->preferSortKeysOnSearch;
    }

    /**
     * Set whether to use the sortKeys to sort items in the feed when a Q query is included in the FeedConfig or the feed request.
     *
     * @param bool $preferSortKeysOnSearch
     */
    public function setPreferSortKeysOnSearch(?bool $preferSortKeysOnSearch)
    {
        $this->preferSortKeysOnSearch = $preferSortKeysOnSearch;
    }

    /**
     * Returns the type of query engine used to retrieve the feed items.
     *
     * @return string
     */
    public function getQueryEngine(): ?string
    {
        return $this->queryEngine;
    }

    /**
     * Set the type of query engine used to retrieve the feed items.
     *
     * @param string $queryEngine
     */
    public function setQueryEngine(?string $queryEngine)
    {
        $this->queryEngine = $queryEngine;
    }

    /**
     * Returns the list of key-value URL parameters that are appended to the feed item's content.urlfield.
     *
     * @return string
     */
    public function getReleaseUrlParameters(): ?string
    {
        return $this->releaseUrlParameters;
    }

    /**
     * Set the list of key-value URL parameters that are appended to the feed item's content.urlfield.
     *
     * @param string $releaseUrlParameters
     */
    public function setReleaseUrlParameters(?string $releaseUrlParameters)
    {
        $this->releaseUrlParameters = $releaseUrlParameters;
    }

    /**
     * Returns the id of the Restriction that is applied to the feed.
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
     * Set the id of the Restriction that is applied to the feed.
     *
     * @param \Psr\Http\Message\UriInterface $restrictionId
     */
    public function setRestrictionId(\Psr\Http\Message\UriInterface $restrictionId)
    {
        $this->restrictionId = $restrictionId;
    }

    /**
     * Returns the object schema version of the feed items.
     *
     * @return string
     */
    public function getSchema(): ?string
    {
        return $this->schema;
    }

    /**
     * Set the object schema version of the feed items.
     *
     * @param string $schema
     */
    public function setSchema(?string $schema)
    {
        $this->schema = $schema;
    }

    /**
     * Returns a query used to select items for the feed. These items are in addition to any specified in the pinnedIds field, and may be filtered by the baseQuery field.
     *
     * @return string[]
     */
    public function getSegmentQueries(): array
    {
        return $this->segmentQueries;
    }

    /**
     * Set a query used to select items for the feed. These items are in addition to any specified in the pinnedIds field, and may be filtered by the baseQuery field.
     *
     * @param string[] $segmentQueries
     */
    public function setSegmentQueries(array $segmentQueries)
    {
        $this->segmentQueries = $segmentQueries;
    }

    /**
     * Returns the list of fields and directions used to sort items in the feed.
     *
     * @return SortKey[]
     */
    public function getSortKeys(): array
    {
        return $this->sortKeys;
    }

    /**
     * Set the list of fields and directions used to sort items in the feed.
     *
     * @param SortKey[] $sortKeys
     */
    public function setSortKeys(array $sortKeys)
    {
        $this->sortKeys = $sortKeys;
    }

    /**
     * Returns the sub feeds of the feed. Sub feeds contain all items of the specified SubFeed.feedType in the feed owner's account. Currently, only a Category sub feed of a Media feed is supported.
     *
     * @return SubFeed[]
     */
    public function getSubFeeds(): array
    {
        return $this->subFeeds;
    }

    /**
     * Set the sub feeds of the feed. Sub feeds contain all items of the specified SubFeed.feedType in the feed owner's account. Currently, only a Category sub feed of a Media feed is supported.
     *
     * @param SubFeed[] $subFeeds
     */
    public function setSubFeeds(array $subFeeds)
    {
        $this->subFeeds = $subFeeds;
    }

    /**
     * Returns the query or key names used to select the thumbnail files for each item in the feed.
     *
     * @return string
     */
    public function getThumbnailFilter(): ?string
    {
        return $this->thumbnailFilter;
    }

    /**
     * Set the query or key names used to select the thumbnail files for each item in the feed.
     *
     * @param string $thumbnailFilter
     */
    public function setThumbnailFilter(?string $thumbnailFilter)
    {
        $this->thumbnailFilter = $thumbnailFilter;
    }

    /**
     * Returns the title of the feed.
     *
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the title of the feed.
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
     * Returns the type of URL that is returned in each feed item's content.urlfield.
     *
     * @return string
     */
    public function getUrlType(): ?string
    {
        return $this->urlType;
    }

    /**
     * Set the type of URL that is returned in each feed item's content.urlfield.
     *
     * @param string $urlType
     */
    public function setUrlType(?string $urlType)
    {
        $this->urlType = $urlType;
    }

    /**
     * Returns whether to include the feed-level elements and any feed-level custom fields in the feed header.
     *
     * @return bool
     */
    public function getValidFeed(): ?bool
    {
        return $this->validFeed;
    }

    /**
     * Set whether to include the feed-level elements and any feed-level custom fields in the feed header.
     *
     * @param bool $validFeed
     */
    public function setValidFeed(?bool $validFeed)
    {
        $this->validFeed = $validFeed;
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
