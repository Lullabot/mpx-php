<?php

namespace Lullabot\Mpx\DataService\Media;

use Lullabot\Mpx\CreateKeyInterface;
use Lullabot\Mpx\DataService\Annotation\DataService;
use Psr\Http\Message\UriInterface;

/**
 * Implements the Media endpoint in the Media data service.
 *
 * @see https://docs.theplatform.com/help/media-media-object
 *
 * @DataService(
 *   service="Media Data Service",
 *   schemaVersion="1.10",
 *   path="/data/Media",
 *   hasAccountContext=true
 * )
 */
class Media implements CreateKeyInterface
{
    /**
     * The id of the AdPolicy associated with this content.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $adPolicyId;

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
     * The administrative workflow tags for this object.
     *
     * @var string[]
     */
    protected $adminTags;

    /**
     * Whether this content is approved for playback.
     *
     * @var bool
     */
    protected $approved;

    /**
     * The creator of this content.
     *
     * @var string
     */
    protected $author;

    /**
     * A map that contains localized versions of this object's author value.
     *
     * @var array
     */
    protected $authorLocalized;

    /**
     * The computed availability of the media for playback.
     *
     * @var AvailabilityState
     */
    protected $availabilityState;

    /**
     * The playback availability tags for this media.
     *
     * @var string[]
     */
    protected $availabilityTags;

    /**
     * An array of distinct time frames that identify the playback availability for this media.
     *
     * @var AvailabilityWindow[]
     */
    protected $availabilityWindows;

    /**
     * The date that this content becomes available for playback.
     *
     * @var \DateTime
     */
    protected $availableDate;

    /**
     * The Category objects that this object is associated with, represented as CategoryInfo objects.
     *
     * @var CategoryInfo[]
     */
    protected $categories;

    /**
     * The id values of the Category objects that this object is associated with.
     *
     * @var \Psr\Http\Message\UriInterface[]
     */
    protected $categoryIds;

    /**
     * Chapter information for this content.
     *
     * @var Chapter[]
     */
    protected $chapters;

    /**
     * The content MediaFile objects that this object is associated with.
     *
     * @var MediaFile[]
     */
    //protected $content;

    /**
     * The copyright holder of this content.
     *
     * @var string
     */
    protected $copyright;

    /**
     * A map that contains localized versions of this object's copyright value.
     *
     * @var array
     */
    protected $copyrightLocalized;

    /**
     * The URL of a copyright statement or terms of use.
     *
     * @var string
     */
    protected $copyrightUrl;

    /**
     * A map that contains localized versions of this object's copyrightUrl value.
     *
     * @var array
     */
    protected $copyrightUrlLocalized;

    /**
     * The list of ISO 3166 country codes that geo-targeting restrictions apply to.
     *
     * @var string[]
     */
    protected $countries;

    /**
     * The creative credits for this content.
     *
     * @var Credit[]
     */
    protected $credits;

    /**
     * The streamingUrl of the default thumbnail for this Media.
     *
     * @var string
     */
    protected $defaultThumbnailUrl;

    /**
     * A description of this content.
     *
     * @var string
     */
    protected $description;

    /**
     * A map that contains localized versions of this object's description value.
     *
     * @var array
     */
    protected $descriptionLocalized;

    /**
     * Whether the specified countries are excluded from playing this content.
     *
     * @var bool
     */
    protected $excludeCountries;

    /**
     * The date that this content expires and is no longer available for playback.
     *
     * @var \DateTime
     */
    protected $expirationDate;

    /**
     * Reserved for future use.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $fileSourceMediaId;

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
     * A list of internal keywords that describe this content.
     *
     * @var string
     */
    protected $keywords;

    /**
     * A map that contains localized versions of this object's keywords value.
     *
     * @var array
     */
    protected $keywordsLocalized;

    /**
     * A link to additional information related to this content.
     *
     * @var string
     */
    protected $link;

    /**
     * A map that contains localized versions of this object's link value.
     *
     * @var array
     */
    protected $linkLocalized;

    /**
     * Whether this object currently allows updates.
     *
     * @var bool
     */
    protected $locked;

    /**
     * The id values of the source Media objects that were shared to create this Media.
     *
     * @var \Psr\Http\Message\UriInterface[]
     */
    protected $originalMediaIds;

    /**
     * The id values of the accounts that shared this Media.
     *
     * @var \Psr\Http\Message\UriInterface[]
     */
    protected $originalOwnerIds;

    /**
     * The id of the account that owns this object.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $ownerId;

    /**
     * The globally unique public identifier for this media.
     *
     * @var string
     */
    protected $pid;

    /**
     * The ID of the Program that represents this media. The GUID URI is recommended.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $programId;

    /**
     * The title of the Provider that represents the account that shared this Media.
     *
     * @var string
     */
    protected $provider;

    /**
     * The id of the Provider that represents the account that shared this Media.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $providerId;

    /**
     * The original release date or airdate of this Media object's content.
     *
     * @var \DateTime
     */
    protected $pubDate;

    /**
     * The public URL for this media.
     *
     * @var string
     */
    protected $publicUrl;

    /**
     * The advisory ratings associated with this content.
     *
     * @var Rating[]
     */
    protected $ratings;

    /**
     * The id of the Restriction associated with this content.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $restrictionId;

    /**
     * The ID of the Program that represents the series to which this media belongs. The GUID URI is recommended.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $seriesId;

    /**
     * Text associated with this content.
     *
     * @var string
     */
    protected $text;

    /**
     * A map that contains localized versions of this object's text value.
     *
     * @var array
     */
    protected $textLocalized;

    /**
     * The thumbnail MediaFile objects that this object is associated with.
     *
     * @var MediaFile[]
     */
    protected $thumbnails;

    /**
     * The name of this object.
     *
     * @var string
     */
    protected $title;

    /**
     * A map that contains localized versions of this object's title value.
     *
     * @var array
     */
    protected $titleLocalized;

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
     * This object's modification version, used for optimistic locking.
     *
     * @var int
     */
    protected $version;

    /**
     * Returns the id of the AdPolicy associated with this content.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getAdPolicyId(): UriInterface
    {
        return $this->adPolicyId;
    }

    /**
     * Set the id of the AdPolicy associated with this content.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setAdPolicyId($adPolicyId)
    {
        $this->adPolicyId = $adPolicyId;
    }

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
     * Returns the administrative workflow tags for this object.
     *
     * @return string[]
     */
    public function getAdminTags(): array
    {
        return $this->adminTags;
    }

    /**
     * Set the administrative workflow tags for this object.
     *
     * @param string[]
     */
    public function setAdminTags($adminTags)
    {
        $this->adminTags = $adminTags;
    }

    /**
     * Returns Whether this content is approved for playback.
     *
     * @return bool
     */
    public function getApproved(): \boolean
    {
        return $this->approved;
    }

    /**
     * Set Whether this content is approved for playback.
     *
     * @param bool
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }

    /**
     * Returns the creator of this content.
     *
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Set the creator of this content.
     *
     * @param string
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * Returns A map that contains localized versions of this object's author value.
     *
     * @return array
     */
    public function getAuthorLocalized(): array
    {
        return $this->authorLocalized;
    }

    /**
     * Set A map that contains localized versions of this object's author value.
     *
     * @param array
     */
    public function setAuthorLocalized($authorLocalized)
    {
        $this->authorLocalized = $authorLocalized;
    }

    /**
     * Returns the computed availability of the media for playback.
     *
     * @return AvailabilityState
     */
    public function getAvailabilityState(): \AvailabilityState
    {
        return $this->availabilityState;
    }

    /**
     * Set the computed availability of the media for playback.
     *
     * @param AvailabilityState
     */
    public function setAvailabilityState($availabilityState)
    {
        $this->availabilityState = $availabilityState;
    }

    /**
     * Returns the playback availability tags for this media.
     *
     * @return string[]
     */
    public function getAvailabilityTags(): array
    {
        return $this->availabilityTags;
    }

    /**
     * Set the playback availability tags for this media.
     *
     * @param string[]
     */
    public function setAvailabilityTags($availabilityTags)
    {
        $this->availabilityTags = $availabilityTags;
    }

    /**
     * Returns An array of distinct time frames that identify the playback availability for this media.
     *
     * @return AvailabilityWindow[]
     */
    public function getAvailabilityWindows(): array
    {
        return $this->availabilityWindows;
    }

    /**
     * Set An array of distinct time frames that identify the playback availability for this media.
     *
     * @param AvailabilityWindow[]
     */
    public function setAvailabilityWindows($availabilityWindows)
    {
        $this->availabilityWindows = $availabilityWindows;
    }

    /**
     * Returns the date that this content becomes available for playback.
     *
     * @return \DateTime
     */
    public function getAvailableDate(): \DateTime
    {
        return $this->availableDate;
    }

    /**
     * Set the date that this content becomes available for playback.
     *
     * @param \DateTime
     */
    public function setAvailableDate($availableDate)
    {
        $this->availableDate = $availableDate;
    }

    /**
     * Returns the Category objects that this object is associated with, represented as CategoryInfo objects.
     *
     * @return CategoryInfo[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * Set the Category objects that this object is associated with, represented as CategoryInfo objects.
     *
     * @param CategoryInfo[]
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * Returns the id values of the Category objects that this object is associated with.
     *
     * @return \Psr\Http\Message\UriInterface[]
     */
    public function getCategoryIds(): array
    {
        return $this->categoryIds;
    }

    /**
     * Set the id values of the Category objects that this object is associated with.
     *
     * @param \Psr\Http\Message\UriInterface[]
     */
    public function setCategoryIds($categoryIds)
    {
        $this->categoryIds = $categoryIds;
    }

    /**
     * Returns Chapter information for this content.
     *
     * @return Chapter[]
     */
    public function getChapters(): array
    {
        return $this->chapters;
    }

    /**
     * Set Chapter information for this content.
     *
     * @param Chapter[]
     */
    public function setChapters($chapters)
    {
        $this->chapters = $chapters;
    }

    /**
     * Returns the content MediaFile objects that this object is associated with.
     *
     * @return MediaFile[]
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * Set the content MediaFile objects that this object is associated with.
     *
     * @param MediaFile[]
     */
    public function setzContent($content)
    {
        $this->content = $content;
    }

    /**
     * Returns the copyright holder of this content.
     *
     * @return string
     */
    public function getCopyright(): string
    {
        return $this->copyright;
    }

    /**
     * Set the copyright holder of this content.
     *
     * @param string
     */
    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;
    }

    /**
     * Returns A map that contains localized versions of this object's copyright value.
     *
     * @return array
     */
    public function getCopyrightLocalized(): array
    {
        return $this->copyrightLocalized;
    }

    /**
     * Set A map that contains localized versions of this object's copyright value.
     *
     * @param array
     */
    public function setCopyrightLocalized($copyrightLocalized)
    {
        $this->copyrightLocalized = $copyrightLocalized;
    }

    /**
     * Returns the URL of a copyright statement or terms of use.
     *
     * @return string
     */
    public function getCopyrightUrl(): string
    {
        return $this->copyrightUrl;
    }

    /**
     * Set the URL of a copyright statement or terms of use.
     *
     * @param string
     */
    public function setCopyrightUrl($copyrightUrl)
    {
        $this->copyrightUrl = $copyrightUrl;
    }

    /**
     * Returns A map that contains localized versions of this object's copyrightUrl value.
     *
     * @return array
     */
    public function getCopyrightUrlLocalized(): array
    {
        return $this->copyrightUrlLocalized;
    }

    /**
     * Set A map that contains localized versions of this object's copyrightUrl value.
     *
     * @param array
     */
    public function setCopyrightUrlLocalized($copyrightUrlLocalized)
    {
        $this->copyrightUrlLocalized = $copyrightUrlLocalized;
    }

    /**
     * Returns the list of ISO 3166 country codes that geo-targeting restrictions apply to.
     *
     * @return string[]
     */
    public function getCountries(): array
    {
        return $this->countries;
    }

    /**
     * Set the list of ISO 3166 country codes that geo-targeting restrictions apply to.
     *
     * @param string[]
     */
    public function setCountries($countries)
    {
        $this->countries = $countries;
    }

    /**
     * Returns the creative credits for this content.
     *
     * @return Credit[]
     */
    public function getCredits(): array
    {
        return $this->credits;
    }

    /**
     * Set the creative credits for this content.
     *
     * @param Credit[]
     */
    public function setCredits($credits)
    {
        $this->credits = $credits;
    }

    /**
     * Returns the streamingUrl of the default thumbnail for this Media.
     *
     * @return string
     */
    public function getDefaultThumbnailUrl(): string
    {
        return $this->defaultThumbnailUrl;
    }

    /**
     * Set the streamingUrl of the default thumbnail for this Media.
     *
     * @param string
     */
    public function setDefaultThumbnailUrl($defaultThumbnailUrl)
    {
        $this->defaultThumbnailUrl = $defaultThumbnailUrl;
    }

    /**
     * Returns A description of this content.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set A description of this content.
     *
     * @param string
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns A map that contains localized versions of this object's description value.
     *
     * @return array
     */
    public function getDescriptionLocalized(): array
    {
        return $this->descriptionLocalized;
    }

    /**
     * Set A map that contains localized versions of this object's description value.
     *
     * @param array
     */
    public function setDescriptionLocalized($descriptionLocalized)
    {
        $this->descriptionLocalized = $descriptionLocalized;
    }

    /**
     * Returns Whether the specified countries are excluded from playing this content.
     *
     * @return bool
     */
    public function getExcludeCountries(): \boolean
    {
        return $this->excludeCountries;
    }

    /**
     * Set Whether the specified countries are excluded from playing this content.
     *
     * @param bool
     */
    public function setExcludeCountries($excludeCountries)
    {
        $this->excludeCountries = $excludeCountries;
    }

    /**
     * Returns the date that this content expires and is no longer available for playback.
     *
     * @return \DateTime
     */
    public function getExpirationDate(): \DateTime
    {
        return $this->expirationDate;
    }

    /**
     * Set the date that this content expires and is no longer available for playback.
     *
     * @param \DateTime
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
    }

    /**
     * Returns Reserved for future use.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getFileSourceMediaId(): UriInterface
    {
        return $this->fileSourceMediaId;
    }

    /**
     * Set Reserved for future use.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setFileSourceMediaId($fileSourceMediaId)
    {
        $this->fileSourceMediaId = $fileSourceMediaId;
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
     * @param \Psr\Http\Message\UriInterface
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns A list of internal keywords that describe this content.
     *
     * @return string
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * Set A list of internal keywords that describe this content.
     *
     * @param string
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * Returns A map that contains localized versions of this object's keywords value.
     *
     * @return array
     */
    public function getKeywordsLocalized(): array
    {
        return $this->keywordsLocalized;
    }

    /**
     * Set A map that contains localized versions of this object's keywords value.
     *
     * @param array
     */
    public function setKeywordsLocalized($keywordsLocalized)
    {
        $this->keywordsLocalized = $keywordsLocalized;
    }

    /**
     * Returns A link to additional information related to this content.
     *
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * Set A link to additional information related to this content.
     *
     * @param string
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * Returns A map that contains localized versions of this object's link value.
     *
     * @return array
     */
    public function getLinkLocalized(): array
    {
        return $this->linkLocalized;
    }

    /**
     * Set A map that contains localized versions of this object's link value.
     *
     * @param array
     */
    public function setLinkLocalized($linkLocalized)
    {
        $this->linkLocalized = $linkLocalized;
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
     * Returns the id values of the source Media objects that were shared to create this Media.
     *
     * @return \Psr\Http\Message\UriInterface[]
     */
    public function getOriginalMediaIds(): array
    {
        return $this->originalMediaIds;
    }

    /**
     * Set the id values of the source Media objects that were shared to create this Media.
     *
     * @param \Psr\Http\Message\UriInterface[]
     */
    public function setOriginalMediaIds($originalMediaIds)
    {
        $this->originalMediaIds = $originalMediaIds;
    }

    /**
     * Returns the id values of the accounts that shared this Media.
     *
     * @return \Psr\Http\Message\UriInterface[]
     */
    public function getOriginalOwnerIds(): array
    {
        return $this->originalOwnerIds;
    }

    /**
     * Set the id values of the accounts that shared this Media.
     *
     * @param \Psr\Http\Message\UriInterface[]
     */
    public function setOriginalOwnerIds($originalOwnerIds)
    {
        $this->originalOwnerIds = $originalOwnerIds;
    }

    /**
     * Returns the id of the account that owns this object.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getOwnerId(): UriInterface
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
     * Returns the globally unique public identifier for this media.
     *
     * @return string
     */
    public function getPid(): string
    {
        return $this->pid;
    }

    /**
     * Set the globally unique public identifier for this media.
     *
     * @param string
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    /**
     * Returns the ID of the Program that represents this media. The GUID URI is recommended.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getProgramId(): UriInterface
    {
        return $this->programId;
    }

    /**
     * Set the ID of the Program that represents this media. The GUID URI is recommended.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setProgramId($programId)
    {
        $this->programId = $programId;
    }

    /**
     * Returns the title of the Provider that represents the account that shared this Media.
     *
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * Set the title of the Provider that represents the account that shared this Media.
     *
     * @param string
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * Returns the id of the Provider that represents the account that shared this Media.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getProviderId(): UriInterface
    {
        return $this->providerId;
    }

    /**
     * Set the id of the Provider that represents the account that shared this Media.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setProviderId($providerId)
    {
        $this->providerId = $providerId;
    }

    /**
     * Returns the original release date or airdate of this Media object's content.
     *
     * @return \DateTime
     */
    public function getPubDate(): \DateTime
    {
        return $this->pubDate;
    }

    /**
     * Set the original release date or airdate of this Media object's content.
     *
     * @param \DateTime
     */
    public function setPubDate($pubDate)
    {
        $this->pubDate = $pubDate;
    }

    /**
     * Returns the public URL for this media.
     *
     * @return string
     */
    public function getPublicUrl(): string
    {
        return $this->publicUrl;
    }

    /**
     * Set the public URL for this media.
     *
     * @param string
     */
    public function setPublicUrl($publicUrl)
    {
        $this->publicUrl = $publicUrl;
    }

    /**
     * Returns the advisory ratings associated with this content.
     *
     * @return Rating[]
     */
    public function getRatings(): array
    {
        return $this->ratings;
    }

    /**
     * Set the advisory ratings associated with this content.
     *
     * @param Rating[]
     */
    public function setRatings($ratings)
    {
        $this->ratings = $ratings;
    }

    /**
     * Returns the id of the Restriction associated with this content.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getRestrictionId(): UriInterface
    {
        return $this->restrictionId;
    }

    /**
     * Set the id of the Restriction associated with this content.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setRestrictionId($restrictionId)
    {
        $this->restrictionId = $restrictionId;
    }

    /**
     * Returns the ID of the Program that represents the series to which this media belongs. The GUID URI is recommended.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getSeriesId(): UriInterface
    {
        return $this->seriesId;
    }

    /**
     * Set the ID of the Program that represents the series to which this media belongs. The GUID URI is recommended.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setSeriesId($seriesId)
    {
        $this->seriesId = $seriesId;
    }

    /**
     * Returns text associated with this content.
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Set text associated with this content.
     *
     * @param string
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Returns A map that contains localized versions of this object's text value.
     *
     * @return array
     */
    public function getTextLocalized(): array
    {
        return $this->textLocalized;
    }

    /**
     * Set A map that contains localized versions of this object's text value.
     *
     * @param array
     */
    public function setTextLocalized($textLocalized)
    {
        $this->textLocalized = $textLocalized;
    }

    /**
     * Returns the thumbnail MediaFile objects that this object is associated with.
     *
     * @return MediaFile[]
     */
    public function getThumbnails(): array
    {
        return $this->thumbnails;
    }

    /**
     * Set the thumbnail MediaFile objects that this object is associated with.
     *
     * @param MediaFile[]
     */
    public function setThumbnails($thumbnails)
    {
        $this->thumbnails = $thumbnails;
    }

    /**
     * Returns the name of this object.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the name of this object.
     *
     * @param string
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns A map that contains localized versions of this object's title value.
     *
     * @return array
     */
    public function getTitleLocalized(): array
    {
        return $this->titleLocalized;
    }

    /**
     * Set A map that contains localized versions of this object's title value.
     *
     * @param array
     */
    public function setTitleLocalized($titleLocalized)
    {
        $this->titleLocalized = $titleLocalized;
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

    /**
     * {@inheritdoc}
     */
    public function getIdKey(): string
    {
        return 'id';
    }

    /**
     * {@inheritdoc}
     */
    public function getCompoundKeys(): array
    {
        return [
            ['ownerId', 'guid'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomKeys(): array
    {
        // @todo Implement getCustomKeys() method.
        return [];
    }
}
