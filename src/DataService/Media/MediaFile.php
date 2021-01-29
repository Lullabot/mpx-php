<?php

namespace Lullabot\Mpx\DataService\Media;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\Annotation\DataService;
use Lullabot\Mpx\DataService\DateTime\NullDateTime;
use Lullabot\Mpx\DataService\ObjectBase;

/**
 * Implements a MediaFile object.
 *
 * @see https://docs.theplatform.com/help/media-mediafile-object
 *
 * @DataService(
 *     service="Media Data Service",
 *     objectType="MediaFile",
 *     schemaVersion="1.10"
 * )
 */
class MediaFile extends ObjectBase
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
     * Whether playback is enabled for this file, and whether it can be associated with new Release objects.
     *
     * @var bool
     */
    protected $allowRelease;

    /**
     * Whether this object is approved.
     *
     * @var bool
     */
    protected $approved;

    /**
     * The file's calculated aspect ratio.
     *
     * @var float
     */
    protected $aspectRatio;

    /**
     * The id values of any AssetType objects associated with this object.
     *
     * @var \Psr\Http\Message\UriInterface[]
     */
    protected $assetTypeIds = [];

    /**
     * The names of any AssetType objects associated with this object.
     *
     * @var string[]
     */
    protected $assetTypes = [];

    /**
     * The number of audio channels the file has.
     *
     * @var int
     */
    protected $audioChannels;

    /**
     * The name of the audio codec the file uses.
     *
     * @var string
     */
    protected $audioCodec;

    /**
     * The file's audio sample rate, in hertz (Hz).
     *
     * @var int
     */
    protected $audioSampleRate;

    /**
     * The file's audio sample size, in bits (b).
     *
     * @var int
     */
    protected $audioSampleSize;

    /**
     * The file's bitrate, in bits per second (bps).
     *
     * @var int
     */
    protected $bitrate;

    /**
     * The file's checksum values.
     *
     * @var array
     */
    protected $checksums;

    /**
     * Whether the file contains text tracks.
     *
     * @var bool
     */
    protected $closedCaptions;

    /**
     * The file's content type.
     *
     * @var string
     */
    protected $contentType;

    /**
     * The description of this object.
     *
     * @var string
     */
    protected $description;

    /**
     * The aspect ratio to display to users.
     *
     * @var string
     */
    protected $displayAspectRatio;

    /**
     * The file's download URL.
     *
     * @var string
     */
    protected $downloadUrl;

    /**
     * The duration of the file, in seconds.
     *
     * @var float
     */
    protected $duration;

    /**
     * Whether the file exists at the specified location yet.
     *
     * @var bool
     */
    protected $exists;

    /**
     * The file's expression type.
     *
     * @var string
     */
    protected $expression;

    /**
     * The secondary source URL for the original file.
     *
     * @var string
     */
    protected $failoverSourceUrl;

    /**
     * One or more secondary streaming URLs.
     *
     * @var string
     */
    protected $failoverStreamingUrl;

    /**
     * The path and filename of the file, relative to a managed storage server's root.
     *
     * @var string
     */
    protected $filePath;

    /**
     * The file's size, in bytes (B).
     *
     * @var int
     */
    protected $fileSize;

    /**
     * The file's format.
     *
     * @var string
     */
    protected $format;

    /**
     * The file's frame rate, in frames per second (fps).
     *
     * @var float
     */
    protected $frameRate;

    /**
     * An alternate identifier for this object that is unique within the owning account.
     *
     * @var string
     */
    protected $guid;

    /**
     * The file's frame height, in pixels.
     *
     * @var int
     */
    protected $height;

    /**
     * The globally unique URI of this object.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $id;

    /**
     * Whether this file is the default content or thumbnail file for the associated Mediaobject.
     *
     * @var bool
     */
    protected $isDefault;

    /**
     * Whether this file is protected.
     *
     * @var bool
     */
    protected $isProtected;

    /**
     * Whether this file is a thumbnail.
     *
     * @var bool
     */
    protected $isThumbnail;

    /**
     * The ISO 639 language code for the file.
     *
     * @var string
     */
    protected $language;

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
     * Information about the file's location used by the FMS moveFile method.
     *
     * @var PreviousLocation[]
     */
    protected $previousLocations = [];

    /**
     * One or more protection system headers.
     *
     * @var string[]
     */
    protected $protectionHeaders = [];

    /**
     * The DRM or copy protection key.
     *
     * @var string
     */
    protected $protectionKey;

    /**
     * The DRM or copy protection scheme.
     *
     * @var string
     */
    protected $protectionScheme;

    /**
     * The Release objects associated with this file.
     *
     * @var Release[]
     */
    protected $releases = [];

    /**
     * The languages in which secondary audio programming (SAP) is available in the file.
     *
     * @var string[]
     */
    protected $secondaryAudio = [];

    /**
     * The format of the streaming content segments.
     *
     * @var string
     */
    protected $segmentFormat;

    /**
     * The id of the Server object representing the storage server that this file is on.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $serverId;

    /**
     * The id of the source MediaFile object that this file was generated from.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $sourceMediaFileId;

    /**
     * The start time of this excerpt in the source file, in seconds.
     *
     * @var float
     */
    protected $sourceTime;

    /**
     * The original source URL for this file.
     *
     * @var string
     */
    protected $sourceUrl;

    /**
     * The URL of the file's storage location.
     *
     * @var string
     */
    protected $storageUrl;

    /**
     * The file's primary streaming URL.
     *
     * @var string
     */
    protected $streamingUrl;

    /**
     * The name of this object.
     *
     * @var string
     */
    protected $title;

    /**
     * The server information required to transfer the file.
     *
     * @var \Lullabot\Mpx\DataService\Media\TransferInfo
     */
    protected $transferInfo;

    /**
     * The id of the encoding template used to generate the file.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $transformId;

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
     * The URL of the file.
     *
     * @var string
     */
    protected $url;

    /**
     * This object's modification version, used for optimistic locking.
     *
     * @var int
     */
    protected $version;

    /**
     * The name of this file's video codec.
     *
     * @var string
     */
    protected $videoCodec;

    /**
     * This file's frame width, in pixels.
     *
     * @var int
     */
    protected $width;

    /**
     * Returns the date and time that this object was created.
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
     */
    public function setAdded(\Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface $added)
    {
        $this->added = $added;
    }

    /**
     * Returns the id of the user that created this object.
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
     */
    public function setAddedByUserId(\Psr\Http\Message\UriInterface $addedByUserId)
    {
        $this->addedByUserId = $addedByUserId;
    }

    /**
     * Returns whether playback is enabled for this file, and whether it can be associated with new Release objects.
     *
     * @return bool
     */
    public function getAllowRelease(): ?bool
    {
        return $this->allowRelease;
    }

    /**
     * Set whether playback is enabled for this file, and whether it can be associated with new Release objects.
     *
     * @param bool $allowRelease
     */
    public function setAllowRelease(?bool $allowRelease)
    {
        $this->allowRelease = $allowRelease;
    }

    /**
     * Returns whether this object is approved.
     *
     * @return bool
     */
    public function getApproved(): ?bool
    {
        return $this->approved;
    }

    /**
     * Set whether this object is approved.
     *
     * @param bool $approved
     */
    public function setApproved(?bool $approved)
    {
        $this->approved = $approved;
    }

    /**
     * Returns the file's calculated aspect ratio.
     *
     * @return float
     */
    public function getAspectRatio(): ?float
    {
        return $this->aspectRatio;
    }

    /**
     * Set the file's calculated aspect ratio.
     *
     * @param float $aspectRatio
     */
    public function setAspectRatio(?float $aspectRatio)
    {
        $this->aspectRatio = $aspectRatio;
    }

    /**
     * Returns the id values of any AssetType objects associated with this object.
     *
     * @return \Psr\Http\Message\UriInterface[]
     */
    public function getAssetTypeIds(): array
    {
        return $this->assetTypeIds;
    }

    /**
     * Set the id values of any AssetType objects associated with this object.
     *
     * @param \Psr\Http\Message\UriInterface[] $assetTypeIds
     */
    public function setAssetTypeIds(array $assetTypeIds)
    {
        $this->assetTypeIds = $assetTypeIds;
    }

    /**
     * Returns the names of any AssetType objects associated with this object.
     *
     * @return string[]
     */
    public function getAssetTypes(): array
    {
        return $this->assetTypes;
    }

    /**
     * Set the names of any AssetType objects associated with this object.
     *
     * @param string[] $assetTypes
     */
    public function setAssetTypes(array $assetTypes)
    {
        $this->assetTypes = $assetTypes;
    }

    /**
     * Returns the number of audio channels the file has.
     *
     * @return int
     */
    public function getAudioChannels(): ?int
    {
        return $this->audioChannels;
    }

    /**
     * Set the number of audio channels the file has.
     *
     * @param int $audioChannels
     */
    public function setAudioChannels(?int $audioChannels)
    {
        $this->audioChannels = $audioChannels;
    }

    /**
     * Returns the name of the audio codec the file uses.
     *
     * @return string
     */
    public function getAudioCodec(): ?string
    {
        return $this->audioCodec;
    }

    /**
     * Set the name of the audio codec the file uses.
     *
     * @param string $audioCodec
     */
    public function setAudioCodec(?string $audioCodec)
    {
        $this->audioCodec = $audioCodec;
    }

    /**
     * Returns the file's audio sample rate, in hertz (Hz).
     *
     * @return int
     */
    public function getAudioSampleRate(): ?int
    {
        return $this->audioSampleRate;
    }

    /**
     * Set the file's audio sample rate, in hertz (Hz).
     *
     * @param int $audioSampleRate
     */
    public function setAudioSampleRate(?int $audioSampleRate)
    {
        $this->audioSampleRate = $audioSampleRate;
    }

    /**
     * Returns the file's audio sample size, in bits (b).
     *
     * @return int
     */
    public function getAudioSampleSize(): ?int
    {
        return $this->audioSampleSize;
    }

    /**
     * Set the file's audio sample size, in bits (b).
     *
     * @param int $audioSampleSize
     */
    public function setAudioSampleSize(?int $audioSampleSize)
    {
        $this->audioSampleSize = $audioSampleSize;
    }

    /**
     * Returns the file's bitrate, in bits per second (bps).
     *
     * @return int
     */
    public function getBitrate(): ?int
    {
        return $this->bitrate;
    }

    /**
     * Set the file's bitrate, in bits per second (bps).
     *
     * @param int $bitrate
     */
    public function setBitrate(?int $bitrate)
    {
        $this->bitrate = $bitrate;
    }

    /**
     * Returns the file's checksum values.
     */
    public function getChecksums(): array
    {
        return $this->checksums;
    }

    /**
     * Set the file's checksum values.
     */
    public function setChecksums(array $checksums)
    {
        $this->checksums = $checksums;
    }

    /**
     * Returns whether the file contains text tracks.
     *
     * @return bool
     */
    public function getClosedCaptions(): ?bool
    {
        return $this->closedCaptions;
    }

    /**
     * Set whether the file contains text tracks.
     *
     * @param bool $closedCaptions
     */
    public function setClosedCaptions(?bool $closedCaptions)
    {
        $this->closedCaptions = $closedCaptions;
    }

    /**
     * Returns the file's content type.
     *
     * @return string
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    /**
     * Set the file's content type.
     *
     * @param string $contentType
     */
    public function setContentType(?string $contentType)
    {
        $this->contentType = $contentType;
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
     * Returns the aspect ratio to display to users.
     *
     * @return string
     */
    public function getDisplayAspectRatio(): ?string
    {
        return $this->displayAspectRatio;
    }

    /**
     * Set the aspect ratio to display to users.
     *
     * @param string $displayAspectRatio
     */
    public function setDisplayAspectRatio(?string $displayAspectRatio)
    {
        $this->displayAspectRatio = $displayAspectRatio;
    }

    /**
     * Returns the file's download URL.
     *
     * @return string
     */
    public function getDownloadUrl(): ?string
    {
        return $this->downloadUrl;
    }

    /**
     * Set the file's download URL.
     *
     * @param string $downloadUrl
     */
    public function setDownloadUrl(?string $downloadUrl)
    {
        $this->downloadUrl = $downloadUrl;
    }

    /**
     * Returns the duration of the file, in seconds.
     *
     * @return float
     */
    public function getDuration(): ?float
    {
        return $this->duration;
    }

    /**
     * Set the duration of the file, in seconds.
     *
     * @param float $duration
     */
    public function setDuration(?float $duration)
    {
        $this->duration = $duration;
    }

    /**
     * Returns whether the file exists at the specified location yet.
     *
     * @return bool
     */
    public function getExists(): ?bool
    {
        return $this->exists;
    }

    /**
     * Set whether the file exists at the specified location yet.
     *
     * @param bool $exists
     */
    public function setExists(?bool $exists)
    {
        $this->exists = $exists;
    }

    /**
     * Returns the file's expression type.
     *
     * @return string
     */
    public function getExpression(): ?string
    {
        return $this->expression;
    }

    /**
     * Set the file's expression type.
     *
     * @param string $expression
     */
    public function setExpression(?string $expression)
    {
        $this->expression = $expression;
    }

    /**
     * Returns the secondary source URL for the original file.
     *
     * @return string
     */
    public function getFailoverSourceUrl(): ?string
    {
        return $this->failoverSourceUrl;
    }

    /**
     * Set the secondary source URL for the original file.
     *
     * @param string $failoverSourceUrl
     */
    public function setFailoverSourceUrl(?string $failoverSourceUrl)
    {
        $this->failoverSourceUrl = $failoverSourceUrl;
    }

    /**
     * Returns one or more secondary streaming URLs.
     *
     * @return string
     */
    public function getFailoverStreamingUrl(): ?string
    {
        return $this->failoverStreamingUrl;
    }

    /**
     * Set one or more secondary streaming URLs.
     *
     * @param string $failoverStreamingUrl
     */
    public function setFailoverStreamingUrl(?string $failoverStreamingUrl)
    {
        $this->failoverStreamingUrl = $failoverStreamingUrl;
    }

    /**
     * Returns the path and filename of the file, relative to a managed storage server's root.
     *
     * @return string
     */
    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    /**
     * Set the path and filename of the file, relative to a managed storage server's root.
     *
     * @param string $filePath
     */
    public function setFilePath(?string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Returns the file's size, in bytes (B).
     *
     * @return int
     */
    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    /**
     * Set the file's size, in bytes (B).
     *
     * @param int $fileSize
     */
    public function setFileSize(?int $fileSize)
    {
        $this->fileSize = $fileSize;
    }

    /**
     * Returns the file's format.
     *
     * @return string
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * Set the file's format.
     *
     * @param string $format
     */
    public function setFormat(?string $format)
    {
        $this->format = $format;
    }

    /**
     * Returns the file's frame rate, in frames per second (fps).
     *
     * @return float
     */
    public function getFrameRate(): ?float
    {
        return $this->frameRate;
    }

    /**
     * Set the file's frame rate, in frames per second (fps).
     *
     * @param float $frameRate
     */
    public function setFrameRate(?float $frameRate)
    {
        $this->frameRate = $frameRate;
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
     * Returns the file's frame height, in pixels.
     *
     * @return int
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * Set the file's frame height, in pixels.
     *
     * @param int $height
     */
    public function setHeight(?int $height)
    {
        $this->height = $height;
    }

    /**
     * Returns the globally unique URI of this object.
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
     */
    public function setId(\Psr\Http\Message\UriInterface $id)
    {
        $this->id = $id;
    }

    /**
     * Returns whether this file is the default content or thumbnail file for the associated Mediaobject.
     *
     * @return bool
     */
    public function getIsDefault(): ?bool
    {
        return $this->isDefault;
    }

    /**
     * Set whether this file is the default content or thumbnail file for the associated Mediaobject.
     *
     * @param bool $isDefault
     */
    public function setIsDefault(?bool $isDefault)
    {
        $this->isDefault = $isDefault;
    }

    /**
     * Returns whether this file is protected.
     *
     * @return bool
     */
    public function getIsProtected(): ?bool
    {
        return $this->isProtected;
    }

    /**
     * Set whether this file is protected.
     *
     * @param bool $isProtected
     */
    public function setIsProtected(?bool $isProtected)
    {
        $this->isProtected = $isProtected;
    }

    /**
     * Returns whether this file is a thumbnail.
     *
     * @return bool
     */
    public function getIsThumbnail(): ?bool
    {
        return $this->isThumbnail;
    }

    /**
     * Set whether this file is a thumbnail.
     *
     * @param bool $isThumbnail
     */
    public function setIsThumbnail(?bool $isThumbnail)
    {
        $this->isThumbnail = $isThumbnail;
    }

    /**
     * Returns the ISO 639 language code for the file.
     *
     * @return string
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * Set the ISO 639 language code for the file.
     *
     * @param string $language
     */
    public function setLanguage(?string $language)
    {
        $this->language = $language;
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
     */
    public function setMediaId(\Psr\Http\Message\UriInterface $mediaId)
    {
        $this->mediaId = $mediaId;
    }

    /**
     * Returns the id of the account that owns this object.
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
     */
    public function setOwnerId(\Psr\Http\Message\UriInterface $ownerId)
    {
        $this->ownerId = $ownerId;
    }

    /**
     * Returns information about the file's location used by the FMS moveFile method.
     *
     * @return PreviousLocation[]
     */
    public function getPreviousLocations(): array
    {
        return $this->previousLocations;
    }

    /**
     * Set information about the file's location used by the FMS moveFile method.
     *
     * @param PreviousLocation[] $previousLocations
     */
    public function setPreviousLocations(array $previousLocations)
    {
        $this->previousLocations = $previousLocations;
    }

    /**
     * Returns one or more protection system headers.
     *
     * @return string[]
     */
    public function getProtectionHeaders(): array
    {
        return $this->protectionHeaders;
    }

    /**
     * Set one or more protection system headers.
     *
     * @param string[] $protectionHeaders
     */
    public function setProtectionHeaders(array $protectionHeaders)
    {
        $this->protectionHeaders = $protectionHeaders;
    }

    /**
     * Returns the DRM or copy protection key.
     *
     * @return string
     */
    public function getProtectionKey(): ?string
    {
        return $this->protectionKey;
    }

    /**
     * Set the DRM or copy protection key.
     *
     * @param string $protectionKey
     */
    public function setProtectionKey(?string $protectionKey)
    {
        $this->protectionKey = $protectionKey;
    }

    /**
     * Returns the DRM or copy protection scheme.
     *
     * @return string
     */
    public function getProtectionScheme(): ?string
    {
        return $this->protectionScheme;
    }

    /**
     * Set the DRM or copy protection scheme.
     *
     * @param string $protectionScheme
     */
    public function setProtectionScheme(?string $protectionScheme)
    {
        $this->protectionScheme = $protectionScheme;
    }

    /**
     * Returns the Release objects associated with this file.
     *
     * @return Release[]
     */
    public function getReleases(): array
    {
        return $this->releases;
    }

    /**
     * Set the Release objects associated with this file.
     *
     * @param Release[] $releases
     */
    public function setReleases(array $releases)
    {
        $this->releases = $releases;
    }

    /**
     * Returns the languages in which secondary audio programming (SAP) is available in the file.
     *
     * @return string[]
     */
    public function getSecondaryAudio(): array
    {
        return $this->secondaryAudio;
    }

    /**
     * Set the languages in which secondary audio programming (SAP) is available in the file.
     *
     * @param string[] $secondaryAudio
     */
    public function setSecondaryAudio(array $secondaryAudio)
    {
        $this->secondaryAudio = $secondaryAudio;
    }

    /**
     * Returns the format of the streaming content segments.
     *
     * @return string
     */
    public function getSegmentFormat(): ?string
    {
        return $this->segmentFormat;
    }

    /**
     * Set the format of the streaming content segments.
     *
     * @param string $segmentFormat
     */
    public function setSegmentFormat(?string $segmentFormat)
    {
        $this->segmentFormat = $segmentFormat;
    }

    /**
     * Returns the id of the Server object representing the storage server that this file is on.
     */
    public function getServerId(): \Psr\Http\Message\UriInterface
    {
        if (!$this->serverId) {
            return new Uri();
        }

        return $this->serverId;
    }

    /**
     * Set the id of the Server object representing the storage server that this file is on.
     */
    public function setServerId(\Psr\Http\Message\UriInterface $serverId)
    {
        $this->serverId = $serverId;
    }

    /**
     * Returns the id of the source MediaFile object that this file was generated from.
     */
    public function getSourceMediaFileId(): \Psr\Http\Message\UriInterface
    {
        if (!$this->sourceMediaFileId) {
            return new Uri();
        }

        return $this->sourceMediaFileId;
    }

    /**
     * Set the id of the source MediaFile object that this file was generated from.
     */
    public function setSourceMediaFileId(\Psr\Http\Message\UriInterface $sourceMediaFileId)
    {
        $this->sourceMediaFileId = $sourceMediaFileId;
    }

    /**
     * Returns the start time of this excerpt in the source file, in seconds.
     *
     * @return float
     */
    public function getSourceTime(): ?float
    {
        return $this->sourceTime;
    }

    /**
     * Set the start time of this excerpt in the source file, in seconds.
     *
     * @param float $sourceTime
     */
    public function setSourceTime(?float $sourceTime)
    {
        $this->sourceTime = $sourceTime;
    }

    /**
     * Returns the original source URL for this file.
     *
     * @return string
     */
    public function getSourceUrl(): ?string
    {
        return $this->sourceUrl;
    }

    /**
     * Set the original source URL for this file.
     *
     * @param string $sourceUrl
     */
    public function setSourceUrl(?string $sourceUrl)
    {
        $this->sourceUrl = $sourceUrl;
    }

    /**
     * Returns the URL of the file's storage location.
     *
     * @return string
     */
    public function getStorageUrl(): ?string
    {
        return $this->storageUrl;
    }

    /**
     * Set the URL of the file's storage location.
     *
     * @param string $storageUrl
     */
    public function setStorageUrl(?string $storageUrl)
    {
        $this->storageUrl = $storageUrl;
    }

    /**
     * Returns the file's primary streaming URL.
     *
     * @return string
     */
    public function getStreamingUrl(): ?string
    {
        return $this->streamingUrl;
    }

    /**
     * Set the file's primary streaming URL.
     *
     * @param string $streamingUrl
     */
    public function setStreamingUrl(?string $streamingUrl)
    {
        $this->streamingUrl = $streamingUrl;
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
     * Returns the server information required to transfer the file.
     *
     * @return \Lullabot\Mpx\DataService\Media\TransferInfo
     */
    public function getTransferInfo(): TransferInfo
    {
        return $this->transferInfo;
    }

    /**
     * Set the server information required to transfer the file.
     *
     * @param \Lullabot\Mpx\DataService\Media\TransferInfo $transferInfo
     */
    public function setTransferInfo(TransferInfo $transferInfo)
    {
        $this->transferInfo = $transferInfo;
    }

    /**
     * Returns the id of the encoding template used to generate the file.
     */
    public function getTransformId(): \Psr\Http\Message\UriInterface
    {
        if (!$this->transformId) {
            return new Uri();
        }

        return $this->transformId;
    }

    /**
     * Set the id of the encoding template used to generate the file.
     */
    public function setTransformId(\Psr\Http\Message\UriInterface $transformId)
    {
        $this->transformId = $transformId;
    }

    /**
     * Returns the date and time this object was last modified.
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
     */
    public function setUpdated(\Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface $updated)
    {
        $this->updated = $updated;
    }

    /**
     * Returns the id of the user that last modified this object.
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
     */
    public function setUpdatedByUserId(\Psr\Http\Message\UriInterface $updatedByUserId)
    {
        $this->updatedByUserId = $updatedByUserId;
    }

    /**
     * Returns the URL of the file.
     *
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Set the URL of the file.
     *
     * @param string $url
     */
    public function setUrl(?string $url)
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

    /**
     * Returns the name of this file's video codec.
     *
     * @return string
     */
    public function getVideoCodec(): ?string
    {
        return $this->videoCodec;
    }

    /**
     * Set the name of this file's video codec.
     *
     * @param string $videoCodec
     */
    public function setVideoCodec(?string $videoCodec)
    {
        $this->videoCodec = $videoCodec;
    }

    /**
     * Returns this file's frame width, in pixels.
     *
     * @return int
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * Set this file's frame width, in pixels.
     *
     * @param int $width
     */
    public function setWidth(?int $width)
    {
        $this->width = $width;
    }
}
