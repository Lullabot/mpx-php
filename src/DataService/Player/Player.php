<?php

namespace Lullabot\Mpx\DataService\Player;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\Annotation\DataService;
use Lullabot\Mpx\DataService\DateTime\NullDateTime;
use Lullabot\Mpx\DataService\ObjectBase;
use Lullabot\Mpx\DataService\PublicIdWithGuidInterface;

/**
 * @DataService(
 *     service="Player Data Service",
 *     objectType="Player",
 *     schemaVersion="1.6",
 * )
 */
class Player extends ObjectBase implements PublicIdWithGuidInterface
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
     * The administrative workflow tags for this object.
     *
     * @var string[]
     */
    protected $adminTags = [];

    /**
     * the identifier for the advertising policy for this object.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $adPolicyId;

    /**
     * Indicates whether the player will feature sharing via email.
     *
     * @var bool
     */
    protected $allowEmail;

    /**
     * Indicates whether the player will provide an embed code for sharing on other sites.
     *
     * @var bool
     */
    protected $allowEmbed;

    /**
     * Indicates whether the player will be allowed to play video in fullscreen mode.
     *
     * @var bool
     */
    protected $allowFullScreen;

    /**
     * Indicates whether the player will make a shareable link available to users.
     *
     * @var bool
     */
    protected $allowGetLink;

    /**
     * Indicates whether the player will make an RSS link available to users.
     *
     * @var bool
     */
    protected $allowRss;

    /**
     * Indicates whether to include the search component.
     *
     * @var bool
     */
    protected $allowSearch;

    /**
     * Indicates whether the player should always display the play overlay.
     *
     * @var bool
     */
    protected $alwaysShowOverlay;

    /**
     * The value of the aspect ratio for the height of the media area.
     *
     * @var int
     */
    protected $aspectRatioHeight;

    /**
     * The value of the aspect ratio for the width of the media area.
     *
     * @var int
     */
    protected $aspectRatioWidth;

    /**
     * Indicates whether the player will start playback on load.
     *
     * @var bool
     */
    protected $autoPlay;

    /**
     * Indicates whether the player will self-initialize on load.
     *
     * @var bool
     */
    protected $autoInitialize;

    /**
     * URL for a custom background image.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $backgroundImageUrl;

    /**
     * Identifier for the color scheme assigned to this player.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $colorSchemeId;

    /**
     * The number of columns in the release list.
     *
     * @var int
     */
    protected $columns;

    /**
     * Reserved for future use. The default value is false.
     *
     * @var bool
     */
    protected $compatibilityMode;

    /**
     * XML layout content for the player control rack.
     *
     * @var string
     */
    protected $controlLayoutXml;

    /**
     * Height of the player control rack.
     *
     * @var int
     */
    protected $controlRackHeight;

    /**
     * Custom CSS content for the player.
     *
     * @var string
     */
    protected $customCss;

    /**
     * URLs to remote custom CSS content for the player.
     *
     * @var \Psr\Http\Message\UriInterface[]
     */
    protected $customCssUrls = [];

    /**
     * Custom HTML content for the player (not currently used).
     *
     * @var string
     */
    protected $customHtml;

    /**
     * Custom JavaScript content for the player.
     *
     * @var string
     */
    protected $customJavaScript;

    /**
     * URLs to custom JavaScript content for the player.
     *
     * @var string[]
     */
    protected $customJavaScriptUrls = [];

    /**
     * Additional attributes to include in the player HTML.
     *
     * @var array
     */
    protected $customProperties;

    /**
     * The description of this object.
     *
     * @var string
     */
    protected $description;

    /**
     * Whether is player is available for customer retrieval.
     *
     * @var bool
     */
    protected $disabled;

    /**
     * The identifier for the advertising policy to use when the player is embedded in another site.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $embedAdPolicyId;

    /**
     * Indicates whether embedded players will be allowed to be viewed in fullscreen mode.
     *
     * @var bool
     */
    protected $embedAllowFullScreen;

    /**
     * The default height of the player when embedded in another site.
     *
     * @var int
     */
    protected $embedHeight;

    /**
     * The identifier for the restriction to apply to this player when embedded in another site.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $embedRestrictionId;

    /**
     * The default width of the player when embedded in another site.
     *
     * @var int
     */
    protected $embedWidth;

    /**
     * The set of plug-ins for this player.
     *
     * @var \Lullabot\Mpx\DataService\Player\PlugInInstance[]
     */
    protected $enabledPlugIns = [];

    /**
     * Indicates if the player responds to commands from an IFrame parent element.
     *
     * @var bool
     */
    protected $enableExternalController;

    /**
     * The URL of the feed that will populate the related items list end card.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $endCardFeedUrl;

    /**
     * The custom failover HTML.
     *
     * @var string
     */
    protected $failoverHtml;

    /**
     * Reserved for future use. The default value is null.
     *
     * @var string
     */
    protected $fallbackPdk;

    /**
     * The URL of the player's default feed.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $feedUrl;

    /**
     * The request parameters to include in the feed request.
     *
     * @var string
     */
    protected $feedUrlParams;

    /**
     * An alternate identifier for this object that is unique within the owning account.
     *
     * @var string
     */
    protected $guid;

    /**
     * The height of the header image.
     *
     * @var int
     */
    protected $headerImageHeight;

    /**
     * The URL of the header image.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $headerImageUrl;

    /**
     * The height of the player.
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
     * Whether to include the default player CSS file in the player HTML page.
     *
     * @var bool
     */
    protected $includeDefaultCss;

    /**
     * The number of items to make visible in each page of the release list.
     *
     * @var int
     */
    protected $itemsPerPage;

    /**
     * The identifier for the layout assigned to this player.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $layoutId;

    /**
     * A list of categories that will appear in the category list.
     *
     * @var string[]
     */
    protected $limitToCategories = [];

    /**
     * The destination URL for when a user clicks the player video area.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $linkUrl;

    /**
     * Whether this object currently allows updates.
     *
     * @var bool
     */
    protected $locked;

    /**
     * The URL of the player overlay image.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $overlayImageUrl;

    /**
     * The id of the account that owns this object.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $ownerId;

    /**
     * The amount of padding to add to the bottom of the player host page.
     *
     * @var int
     */
    protected $paddingBottom;

    /**
     * The amount of padding to add to the left side of the player host page.
     *
     * @var int
     */
    protected $paddingLeft;

    /**
     * The amount of padding to add to the right side of the player host page.
     *
     * @var int
     */
    protected $paddingRight;

    /**
     * The amount of padding to add to the top of the player host page.
     *
     * @var int
     */
    protected $paddingTop;

    /**
     * Reserved for future use. The default value is null.
     *
     * @var string
     */
    protected $pdk;

    /**
     * The public identifier for this player when requested through the Player Service.
     *
     * @var string
     */
    protected $pid;

    /**
     * Indicates if the player should automatically play the next release when one finishes.
     *
     * @var bool
     */
    protected $playAll;

    /**
     * Player URL used in the sharing features.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $playerUrl;

    /**
     * The default height to use for the poster image.
     *
     * @var int
     */
    protected $posterImageDefaultHeight;

    /**
     * The default width to use for the poster image.
     *
     * @var int
     */
    protected $posterImageDefaultWidth;

    /**
     * The meta asset type to use for the poster image.
     *
     * @var string
     */
    protected $posterImageMetaAssetType;

    /**
     * The preview asset type to use for the poster image.
     *
     * @var string
     */
    protected $posterImagePreviewAssetType;

    /**
     * The media formats meant for use in this player.
     *
     * @var string[]
     */
    protected $preferredFormats = [];

    /**
     * The runtimes meant for use in the player.
     *
     * @var string[]
     */
    protected $preferredRuntimes = [];

    /**
     * Heights of the regions in the layout.
     *
     * @var array
     */
    protected $regionHeights;

    /**
     * Widths of the regions in the layout.
     *
     * @var array
     */
    protected $regionWidths;

    /**
     * URL parameters to add to Public URL requests.
     *
     * @var string
     */
    protected $releaseUrlParams;

    /**
     * The identifier of the restriction to apply to this player.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $restrictionId;

    /**
     * Indicates whether to display the air date in the release list component.
     *
     * @var bool
     */
    protected $showAirdate;

    /**
     * Indicates whether to include the All option in the category list component.
     *
     * @var bool
     */
    protected $showAllChoice;

    /**
     * Indicates whether to display the author in the release list and clip info components.
     *
     * @var bool
     */
    protected $showAuthor;

    /**
     * Indicates whether to display the media bitrate in the release list component.
     *
     * @var bool
     */
    protected $showBitrate;

    /**
     * Indicates whether to show the full video time in the player control rack.
     *
     * @var bool
     */
    protected $showFullTime;

    /**
     * Indicates whether to show the Most Popular option in the category list component.
     *
     * @var bool
     */
    protected $showMostPopularChoice;

    /**
     * Indicates whether to display the previous and next buttons in the player control rack.
     *
     * @var bool
     */
    protected $showNav;

    /**
     * Indicates whether to randomize the contents of the release list.
     *
     * @var bool
     */
    protected $shuffle;

    /**
     * Identifier for the skin object to apply this player.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $skinId;

    /**
     * The height of the thumbnail images in the release list.
     *
     * @var int
     */
    protected $thumbnailHeight;

    /**
     * The width of the thumbnail images in the release list.
     *
     * @var int
     */
    protected $thumbnailWidth;

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
     * Indicates whether to float the control rack over the media area of the player.
     *
     * @var bool
     */
    protected $useFloatingControls;

    /**
     * This object's modification version, used for optimistic locking.
     *
     * @var int
     */
    protected $version;

    /**
     * The width of the player.
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
     * @param string[] $adminTags
     */
    public function setAdminTags(array $adminTags)
    {
        $this->adminTags = $adminTags;
    }

    /**
     * Returns the identifier for the advertising policy for this object.
     */
    public function getAdPolicyId(): \Psr\Http\Message\UriInterface
    {
        if (!$this->adPolicyId) {
            return new Uri();
        }

        return $this->adPolicyId;
    }

    /**
     * Set the identifier for the advertising policy for this object.
     */
    public function setAdPolicyId(\Psr\Http\Message\UriInterface $adPolicyId)
    {
        $this->adPolicyId = $adPolicyId;
    }

    /**
     * Returns indicates whether the player will feature sharing via email.
     */
    public function getAllowEmail(): ?bool
    {
        return $this->allowEmail;
    }

    /**
     * Set indicates whether the player will feature sharing via email.
     */
    public function setAllowEmail(?bool $allowEmail)
    {
        $this->allowEmail = $allowEmail;
    }

    /**
     * Returns indicates whether the player will provide an embed code for sharing on other sites.
     */
    public function getAllowEmbed(): ?bool
    {
        return $this->allowEmbed;
    }

    /**
     * Set indicates whether the player will provide an embed code for sharing on other sites.
     */
    public function setAllowEmbed(?bool $allowEmbed)
    {
        $this->allowEmbed = $allowEmbed;
    }

    /**
     * Returns indicates whether the player will be allowed to play video in fullscreen mode.
     */
    public function getAllowFullScreen(): ?bool
    {
        return $this->allowFullScreen;
    }

    /**
     * Set indicates whether the player will be allowed to play video in fullscreen mode.
     */
    public function setAllowFullScreen(?bool $allowFullScreen)
    {
        $this->allowFullScreen = $allowFullScreen;
    }

    /**
     * Returns indicates whether the player will make a shareable link available to users.
     */
    public function getAllowGetLink(): ?bool
    {
        return $this->allowGetLink;
    }

    /**
     * Set indicates whether the player will make a shareable link available to users.
     */
    public function setAllowGetLink(?bool $allowGetLink)
    {
        $this->allowGetLink = $allowGetLink;
    }

    /**
     * Returns indicates whether the player will make an RSS link available to users.
     */
    public function getAllowRss(): ?bool
    {
        return $this->allowRss;
    }

    /**
     * Set indicates whether the player will make an RSS link available to users.
     */
    public function setAllowRss(?bool $allowRss)
    {
        $this->allowRss = $allowRss;
    }

    /**
     * Returns indicates whether to include the search component.
     */
    public function getAllowSearch(): ?bool
    {
        return $this->allowSearch;
    }

    /**
     * Set indicates whether to include the search component.
     */
    public function setAllowSearch(?bool $allowSearch)
    {
        $this->allowSearch = $allowSearch;
    }

    /**
     * Returns indicates whether the player should always display the play overlay.
     */
    public function getAlwaysShowOverlay(): ?bool
    {
        return $this->alwaysShowOverlay;
    }

    /**
     * Set indicates whether the player should always display the play overlay.
     */
    public function setAlwaysShowOverlay(?bool $alwaysShowOverlay)
    {
        $this->alwaysShowOverlay = $alwaysShowOverlay;
    }

    /**
     * Returns the value of the aspect ratio for the height of the media area.
     */
    public function getAspectRatioHeight(): ?int
    {
        return $this->aspectRatioHeight;
    }

    /**
     * Set the value of the aspect ratio for the height of the media area.
     */
    public function setAspectRatioHeight(?int $aspectRatioHeight)
    {
        $this->aspectRatioHeight = $aspectRatioHeight;
    }

    /**
     * Returns the value of the aspect ratio for the width of the media area.
     */
    public function getAspectRatioWidth(): ?int
    {
        return $this->aspectRatioWidth;
    }

    /**
     * Set the value of the aspect ratio for the width of the media area.
     */
    public function setAspectRatioWidth(?int $aspectRatioWidth)
    {
        $this->aspectRatioWidth = $aspectRatioWidth;
    }

    /**
     * Returns indicates whether the player will start playback on load.
     */
    public function getAutoPlay(): ?bool
    {
        return $this->autoPlay;
    }

    /**
     * Set indicates whether the player will start playback on load.
     */
    public function setAutoPlay(?bool $autoPlay)
    {
        $this->autoPlay = $autoPlay;
    }

    /**
     * Returns indicates whether the player will self-initialize on load.
     */
    public function getAutoInitialize(): ?bool
    {
        return $this->autoInitialize;
    }

    /**
     * Set indicates whether the player will self-initialize on load.
     */
    public function setAutoInitialize(?bool $autoInitialize)
    {
        $this->autoInitialize = $autoInitialize;
    }

    /**
     * Returns URL for a custom background image.
     */
    public function getBackgroundImageUrl(): ?string
    {
        return $this->backgroundImageUrl;
    }

    /**
     * Set URL for a custom background image.
     */
    public function setBackgroundImageUrl(?string $backgroundImageUrl)
    {
        $this->backgroundImageUrl = $backgroundImageUrl;
    }

    /**
     * Returns identifier for the color scheme assigned to this player.
     */
    public function getColorSchemeId(): \Psr\Http\Message\UriInterface
    {
        if (!$this->colorSchemeId) {
            return new Uri();
        }

        return $this->colorSchemeId;
    }

    /**
     * Set identifier for the color scheme assigned to this player.
     */
    public function setColorSchemeId(\Psr\Http\Message\UriInterface $colorSchemeId)
    {
        $this->colorSchemeId = $colorSchemeId;
    }

    /**
     * Returns the number of columns in the release list.
     */
    public function getColumns(): ?int
    {
        return $this->columns;
    }

    /**
     * Set the number of columns in the release list.
     */
    public function setColumns(?int $columns)
    {
        $this->columns = $columns;
    }

    /**
     * Returns reserved for future use. The default value is false.
     */
    public function getCompatibilityMode(): ?bool
    {
        return $this->compatibilityMode;
    }

    /**
     * Set reserved for future use. The default value is false.
     */
    public function setCompatibilityMode(?bool $compatibilityMode)
    {
        $this->compatibilityMode = $compatibilityMode;
    }

    /**
     * Returns XML layout content for the player control rack.
     */
    public function getControlLayoutXml(): ?string
    {
        return $this->controlLayoutXml;
    }

    /**
     * Set XML layout content for the player control rack.
     */
    public function setControlLayoutXml(?string $controlLayoutXml)
    {
        $this->controlLayoutXml = $controlLayoutXml;
    }

    /**
     * Returns height of the player control rack.
     */
    public function getControlRackHeight(): ?int
    {
        return $this->controlRackHeight;
    }

    /**
     * Set height of the player control rack.
     */
    public function setControlRackHeight(?int $controlRackHeight)
    {
        $this->controlRackHeight = $controlRackHeight;
    }

    /**
     * Returns custom CSS content for the player.
     */
    public function getCustomCss(): ?string
    {
        return $this->customCss;
    }

    /**
     * Set custom CSS content for the player.
     */
    public function setCustomCss(?string $customCss)
    {
        $this->customCss = $customCss;
    }

    /**
     * Returns URLs to remote custom CSS content for the player.
     *
     * @return string[]
     */
    public function getCustomCssUrls(): array
    {
        return $this->customCssUrls;
    }

    /**
     * Set URLs to remote custom CSS content for the player.
     *
     * @param string[] $customCssUrls
     */
    public function setCustomCssUrls(array $customCssUrls)
    {
        $this->customCssUrls = $customCssUrls;
    }

    /**
     * Returns custom HTML content for the player (not currently used).
     */
    public function getCustomHtml(): ?string
    {
        return $this->customHtml;
    }

    /**
     * Set custom HTML content for the player (not currently used).
     */
    public function setCustomHtml(?string $customHtml)
    {
        $this->customHtml = $customHtml;
    }

    /**
     * Returns custom JavaScript content for the player.
     */
    public function getCustomJavaScript(): ?string
    {
        return $this->customJavaScript;
    }

    /**
     * Set custom JavaScript content for the player.
     */
    public function setCustomJavaScript(?string $customJavaScript)
    {
        $this->customJavaScript = $customJavaScript;
    }

    /**
     * Returns URLs to custom JavaScript content for the player.
     *
     * @return string[]
     */
    public function getCustomJavaScriptUrls(): array
    {
        return $this->customJavaScriptUrls;
    }

    /**
     * Set URLs to custom JavaScript content for the player.
     *
     * @param string[] $customJavaScriptUrls
     */
    public function setCustomJavaScriptUrls(array $customJavaScriptUrls)
    {
        $this->customJavaScriptUrls = $customJavaScriptUrls;
    }

    /**
     * Returns additional attributes to include in the player HTML.
     */
    public function getCustomProperties(): array
    {
        return $this->customProperties;
    }

    /**
     * Set additional attributes to include in the player HTML.
     */
    public function setCustomProperties(array $customProperties)
    {
        $this->customProperties = $customProperties;
    }

    /**
     * Returns the description of this object.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the description of this object.
     */
    public function setDescription(?string $description)
    {
        $this->description = $description;
    }

    /**
     * Returns whether is player is available for customer retrieval.
     */
    public function getDisabled(): ?bool
    {
        return $this->disabled;
    }

    /**
     * Set whether is player is available for customer retrieval.
     */
    public function setDisabled(?bool $disabled)
    {
        $this->disabled = $disabled;
    }

    /**
     * Returns the identifier for the advertising policy to use when the player is embedded in another site.
     */
    public function getEmbedAdPolicyId(): \Psr\Http\Message\UriInterface
    {
        if (!$this->embedAdPolicyId) {
            return new Uri();
        }

        return $this->embedAdPolicyId;
    }

    /**
     * Set the identifier for the advertising policy to use when the player is embedded in another site.
     */
    public function setEmbedAdPolicyId(\Psr\Http\Message\UriInterface $embedAdPolicyId)
    {
        $this->embedAdPolicyId = $embedAdPolicyId;
    }

    /**
     * Returns indicates whether embedded players will be allowed to be viewed in fullscreen mode.
     */
    public function getEmbedAllowFullScreen(): ?bool
    {
        return $this->embedAllowFullScreen;
    }

    /**
     * Set indicates whether embedded players will be allowed to be viewed in fullscreen mode.
     */
    public function setEmbedAllowFullScreen(?bool $embedAllowFullScreen)
    {
        $this->embedAllowFullScreen = $embedAllowFullScreen;
    }

    /**
     * Returns the default height of the player when embedded in another site.
     */
    public function getEmbedHeight(): ?int
    {
        return $this->embedHeight;
    }

    /**
     * Set the default height of the player when embedded in another site.
     */
    public function setEmbedHeight(?int $embedHeight)
    {
        $this->embedHeight = $embedHeight;
    }

    /**
     * Returns the identifier for the restriction to apply to this player when embedded in another site.
     */
    public function getEmbedRestrictionId(): \Psr\Http\Message\UriInterface
    {
        if (!$this->embedRestrictionId) {
            return new Uri();
        }

        return $this->embedRestrictionId;
    }

    /**
     * Set the identifier for the restriction to apply to this player when embedded in another site.
     */
    public function setEmbedRestrictionId(\Psr\Http\Message\UriInterface $embedRestrictionId)
    {
        $this->embedRestrictionId = $embedRestrictionId;
    }

    /**
     * Returns the default width of the player when embedded in another site.
     */
    public function getEmbedWidth(): ?int
    {
        return $this->embedWidth;
    }

    /**
     * Set the default width of the player when embedded in another site.
     */
    public function setEmbedWidth(?int $embedWidth)
    {
        $this->embedWidth = $embedWidth;
    }

    /**
     * Returns the set of plug-ins for this player.
     *
     * @return PlugInInstance[]
     */
    public function getEnabledPlugIns(): array
    {
        return $this->enabledPlugIns;
    }

    /**
     * Set the set of plug-ins for this player.
     *
     * @param PlugInInstance[] $enabledPlugIns
     */
    public function setEnabledPlugIns(array $enabledPlugIns)
    {
        $this->enabledPlugIns = $enabledPlugIns;
    }

    /**
     * Returns indicates if the player responds to commands from an IFrame parent element.
     */
    public function getEnableExternalController(): ?bool
    {
        return $this->enableExternalController;
    }

    /**
     * Set indicates if the player responds to commands from an IFrame parent element.
     */
    public function setEnableExternalController(?bool $enableExternalController)
    {
        $this->enableExternalController = $enableExternalController;
    }

    /**
     * Returns the URL of the feed that will populate the related items list end card.
     */
    public function getEndCardFeedUrl(): ?string
    {
        return $this->endCardFeedUrl;
    }

    /**
     * Set the URL of the feed that will populate the related items list end card.
     */
    public function setEndCardFeedUrl(?string $endCardFeedUrl)
    {
        $this->endCardFeedUrl = $endCardFeedUrl;
    }

    /**
     * Returns the custom failover HTML.
     */
    public function getFailoverHtml(): ?string
    {
        return $this->failoverHtml;
    }

    /**
     * Set the custom failover HTML.
     */
    public function setFailoverHtml(?string $failoverHtml)
    {
        $this->failoverHtml = $failoverHtml;
    }

    /**
     * Returns reserved for future use. The default value is null.
     */
    public function getFallbackPdk(): ?string
    {
        return $this->fallbackPdk;
    }

    /**
     * Set reserved for future use. The default value is null.
     */
    public function setFallbackPdk(?string $fallbackPdk)
    {
        $this->fallbackPdk = $fallbackPdk;
    }

    /**
     * Returns the URL of the player's default feed.
     */
    public function getFeedUrl(): ?string
    {
        return $this->feedUrl;
    }

    /**
     * Set the URL of the player's default feed.
     */
    public function setFeedUrl(?string $feedUrl)
    {
        $this->feedUrl = $feedUrl;
    }

    /**
     * Returns the request parameters to include in the feed request.
     */
    public function getFeedUrlParams(): ?string
    {
        return $this->feedUrlParams;
    }

    /**
     * Set the request parameters to include in the feed request.
     */
    public function setFeedUrlParams(?string $feedUrlParams)
    {
        $this->feedUrlParams = $feedUrlParams;
    }

    /**
     * Returns an alternate identifier for this object that is unique within the owning account.
     */
    public function getGuid(): ?string
    {
        return $this->guid;
    }

    /**
     * Set an alternate identifier for this object that is unique within the owning account.
     */
    public function setGuid(?string $guid)
    {
        $this->guid = $guid;
    }

    /**
     * Returns the height of the header image.
     */
    public function getHeaderImageHeight(): ?int
    {
        return $this->headerImageHeight;
    }

    /**
     * Set the height of the header image.
     */
    public function setHeaderImageHeight(?int $headerImageHeight)
    {
        $this->headerImageHeight = $headerImageHeight;
    }

    /**
     * Returns the URL of the header image.
     */
    public function getHeaderImageUrl(): ?string
    {
        return $this->headerImageUrl;
    }

    /**
     * Set the URL of the header image.
     */
    public function setHeaderImageUrl(?string $headerImageUrl)
    {
        $this->headerImageUrl = $headerImageUrl;
    }

    /**
     * Returns the height of the player.
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * Set the height of the player.
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
     * Returns whether to include the default player CSS file in the player HTML page.
     */
    public function getIncludeDefaultCss(): ?bool
    {
        return $this->includeDefaultCss;
    }

    /**
     * Set whether to include the default player CSS file in the player HTML page.
     */
    public function setIncludeDefaultCss(?bool $includeDefaultCss)
    {
        $this->includeDefaultCss = $includeDefaultCss;
    }

    /**
     * Returns the number of items to make visible in each page of the release list.
     */
    public function getItemsPerPage(): ?int
    {
        return $this->itemsPerPage;
    }

    /**
     * Set the number of items to make visible in each page of the release list.
     */
    public function setItemsPerPage(?int $itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * Returns the identifier for the layout assigned to this player.
     */
    public function getLayoutId(): \Psr\Http\Message\UriInterface
    {
        if (!$this->layoutId) {
            return new Uri();
        }

        return $this->layoutId;
    }

    /**
     * Set the identifier for the layout assigned to this player.
     */
    public function setLayoutId(\Psr\Http\Message\UriInterface $layoutId)
    {
        $this->layoutId = $layoutId;
    }

    /**
     * Returns a list of categories that will appear in the category list.
     *
     * @return string[]
     */
    public function getLimitToCategories(): array
    {
        return $this->limitToCategories;
    }

    /**
     * Set a list of categories that will appear in the category list.
     *
     * @param string[] $limitToCategories
     */
    public function setLimitToCategories(array $limitToCategories)
    {
        $this->limitToCategories = $limitToCategories;
    }

    /**
     * Returns the destination URL for when a user clicks the player video area.
     */
    public function getLinkUrl(): ?string
    {
        return $this->linkUrl;
    }

    /**
     * Set the destination URL for when a user clicks the player video area.
     */
    public function setLinkUrl(?string $linkUrl)
    {
        $this->linkUrl = $linkUrl;
    }

    /**
     * Returns whether this object currently allows updates.
     */
    public function getLocked(): ?bool
    {
        return $this->locked;
    }

    /**
     * Set whether this object currently allows updates.
     */
    public function setLocked(?bool $locked)
    {
        $this->locked = $locked;
    }

    /**
     * Returns the URL of the player overlay image.
     */
    public function getOverlayImageUrl(): ?string
    {
        return $this->overlayImageUrl;
    }

    /**
     * Set the URL of the player overlay image.
     */
    public function setOverlayImageUrl(?string $overlayImageUrl)
    {
        $this->overlayImageUrl = $overlayImageUrl;
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
     * Returns the amount of padding to add to the bottom of the player host page.
     */
    public function getPaddingBottom(): ?int
    {
        return $this->paddingBottom;
    }

    /**
     * Set the amount of padding to add to the bottom of the player host page.
     */
    public function setPaddingBottom(?int $paddingBottom)
    {
        $this->paddingBottom = $paddingBottom;
    }

    /**
     * Returns the amount of padding to add to the left side of the player host page.
     */
    public function getPaddingLeft(): ?int
    {
        return $this->paddingLeft;
    }

    /**
     * Set the amount of padding to add to the left side of the player host page.
     */
    public function setPaddingLeft(?int $paddingLeft)
    {
        $this->paddingLeft = $paddingLeft;
    }

    /**
     * Returns the amount of padding to add to the right side of the player host page.
     */
    public function getPaddingRight(): ?int
    {
        return $this->paddingRight;
    }

    /**
     * Set the amount of padding to add to the right side of the player host page.
     */
    public function setPaddingRight(?int $paddingRight)
    {
        $this->paddingRight = $paddingRight;
    }

    /**
     * Returns the amount of padding to add to the top of the player host page.
     */
    public function getPaddingTop(): ?int
    {
        return $this->paddingTop;
    }

    /**
     * Set the amount of padding to add to the top of the player host page.
     */
    public function setPaddingTop(?int $paddingTop)
    {
        $this->paddingTop = $paddingTop;
    }

    /**
     * Returns reserved for future use. The default value is null.
     */
    public function getPdk(): ?string
    {
        return $this->pdk;
    }

    /**
     * Set reserved for future use. The default value is null.
     */
    public function setPdk(?string $pdk)
    {
        $this->pdk = $pdk;
    }

    /**
     * Returns the public identifier for this player when requested through the Player Service.
     */
    public function getPid(): ?string
    {
        return $this->pid;
    }

    /**
     * Set the public identifier for this player when requested through the Player Service.
     */
    public function setPid(?string $pid)
    {
        $this->pid = $pid;
    }

    /**
     * Returns indicates if the player should automatically play the next release when one finishes.
     */
    public function getPlayAll(): ?bool
    {
        return $this->playAll;
    }

    /**
     * Set indicates if the player should automatically play the next release when one finishes.
     */
    public function setPlayAll(?bool $playAll)
    {
        $this->playAll = $playAll;
    }

    /**
     * Returns player URL used in the sharing features.
     */
    public function getPlayerUrl(): ?string
    {
        return $this->playerUrl;
    }

    /**
     * Set player URL used in the sharing features.
     */
    public function setPlayerUrl(?string $playerUrl)
    {
        $this->playerUrl = $playerUrl;
    }

    /**
     * Returns the default height to use for the poster image.
     */
    public function getPosterImageDefaultHeight(): ?int
    {
        return $this->posterImageDefaultHeight;
    }

    /**
     * Set the default height to use for the poster image.
     */
    public function setPosterImageDefaultHeight(?int $posterImageDefaultHeight)
    {
        $this->posterImageDefaultHeight = $posterImageDefaultHeight;
    }

    /**
     * Returns the default width to use for the poster image.
     */
    public function getPosterImageDefaultWidth(): ?int
    {
        return $this->posterImageDefaultWidth;
    }

    /**
     * Set the default width to use for the poster image.
     */
    public function setPosterImageDefaultWidth(?int $posterImageDefaultWidth)
    {
        $this->posterImageDefaultWidth = $posterImageDefaultWidth;
    }

    /**
     * Returns the meta asset type to use for the poster image.
     */
    public function getPosterImageMetaAssetType(): ?string
    {
        return $this->posterImageMetaAssetType;
    }

    /**
     * Set the meta asset type to use for the poster image.
     */
    public function setPosterImageMetaAssetType(?string $posterImageMetaAssetType)
    {
        $this->posterImageMetaAssetType = $posterImageMetaAssetType;
    }

    /**
     * Returns the preview asset type to use for the poster image.
     */
    public function getPosterImagePreviewAssetType(): ?string
    {
        return $this->posterImagePreviewAssetType;
    }

    /**
     * Set the preview asset type to use for the poster image.
     */
    public function setPosterImagePreviewAssetType(?string $posterImagePreviewAssetType)
    {
        $this->posterImagePreviewAssetType = $posterImagePreviewAssetType;
    }

    /**
     * Returns the media formats meant for use in this player.
     *
     * @return string[]
     */
    public function getPreferredFormats(): array
    {
        return $this->preferredFormats;
    }

    /**
     * Set the media formats meant for use in this player.
     *
     * @param string[] $preferredFormats
     */
    public function setPreferredFormats(array $preferredFormats)
    {
        $this->preferredFormats = $preferredFormats;
    }

    /**
     * Returns the runtimes meant for use in the player.
     *
     * @return string[]
     */
    public function getPreferredRuntimes(): array
    {
        return $this->preferredRuntimes;
    }

    /**
     * Set the runtimes meant for use in the player.
     *
     * @param string[] $preferredRuntimes
     */
    public function setPreferredRuntimes(array $preferredRuntimes)
    {
        $this->preferredRuntimes = $preferredRuntimes;
    }

    /**
     * Returns heights of the regions in the layout.
     */
    public function getRegionHeights(): array
    {
        return $this->regionHeights;
    }

    /**
     * Set heights of the regions in the layout.
     */
    public function setRegionHeights(array $regionHeights)
    {
        $this->regionHeights = $regionHeights;
    }

    /**
     * Returns widths of the regions in the layout.
     */
    public function getRegionWidths(): array
    {
        return $this->regionWidths;
    }

    /**
     * Set widths of the regions in the layout.
     */
    public function setRegionWidths(array $regionWidths)
    {
        $this->regionWidths = $regionWidths;
    }

    /**
     * Returns URL parameters to add to Public URL requests.
     */
    public function getReleaseUrlParams(): ?string
    {
        return $this->releaseUrlParams;
    }

    /**
     * Set URL parameters to add to Public URL requests.
     */
    public function setReleaseUrlParams(?string $releaseUrlParams)
    {
        $this->releaseUrlParams = $releaseUrlParams;
    }

    /**
     * Returns the identifier of the restriction to apply to this player.
     */
    public function getRestrictionId(): \Psr\Http\Message\UriInterface
    {
        if (!$this->restrictionId) {
            return new Uri();
        }

        return $this->restrictionId;
    }

    /**
     * Set the identifier of the restriction to apply to this player.
     */
    public function setRestrictionId(\Psr\Http\Message\UriInterface $restrictionId)
    {
        $this->restrictionId = $restrictionId;
    }

    /**
     * Returns indicates whether to display the air date in the release list component.
     */
    public function getShowAirdate(): ?bool
    {
        return $this->showAirdate;
    }

    /**
     * Set indicates whether to display the air date in the release list component.
     */
    public function setShowAirdate(?bool $showAirdate)
    {
        $this->showAirdate = $showAirdate;
    }

    /**
     * Returns indicates whether to include the All option in the category list component.
     */
    public function getShowAllChoice(): ?bool
    {
        return $this->showAllChoice;
    }

    /**
     * Set indicates whether to include the All option in the category list component.
     */
    public function setShowAllChoice(?bool $showAllChoice)
    {
        $this->showAllChoice = $showAllChoice;
    }

    /**
     * Returns indicates whether to display the author in the release list and clip info components.
     */
    public function getShowAuthor(): ?bool
    {
        return $this->showAuthor;
    }

    /**
     * Set indicates whether to display the author in the release list and clip info components.
     */
    public function setShowAuthor(?bool $showAuthor)
    {
        $this->showAuthor = $showAuthor;
    }

    /**
     * Returns indicates whether to display the media bitrate in the release list component.
     */
    public function getShowBitrate(): ?bool
    {
        return $this->showBitrate;
    }

    /**
     * Set indicates whether to display the media bitrate in the release list component.
     */
    public function setShowBitrate(?bool $showBitrate)
    {
        $this->showBitrate = $showBitrate;
    }

    /**
     * Returns indicates whether to show the full video time in the player control rack.
     */
    public function getShowFullTime(): ?bool
    {
        return $this->showFullTime;
    }

    /**
     * Set indicates whether to show the full video time in the player control rack.
     */
    public function setShowFullTime(?bool $showFullTime)
    {
        $this->showFullTime = $showFullTime;
    }

    /**
     * Returns indicates whether to show the Most Popular option in the category list component.
     */
    public function getShowMostPopularChoice(): ?bool
    {
        return $this->showMostPopularChoice;
    }

    /**
     * Set indicates whether to show the Most Popular option in the category list component.
     */
    public function setShowMostPopularChoice(?bool $showMostPopularChoice)
    {
        $this->showMostPopularChoice = $showMostPopularChoice;
    }

    /**
     * Returns indicates whether to display the previous and next buttons in the player control rack.
     */
    public function getShowNav(): ?bool
    {
        return $this->showNav;
    }

    /**
     * Set indicates whether to display the previous and next buttons in the player control rack.
     */
    public function setShowNav(?bool $showNav)
    {
        $this->showNav = $showNav;
    }

    /**
     * Returns indicates whether to randomize the contents of the release list.
     */
    public function getShuffle(): ?bool
    {
        return $this->shuffle;
    }

    /**
     * Set indicates whether to randomize the contents of the release list.
     */
    public function setShuffle(?bool $shuffle)
    {
        $this->shuffle = $shuffle;
    }

    /**
     * Returns identifier for the skin object to apply this player.
     */
    public function getSkinId(): \Psr\Http\Message\UriInterface
    {
        if (!$this->skinId) {
            return new Uri();
        }

        return $this->skinId;
    }

    /**
     * Set identifier for the skin object to apply this player.
     */
    public function setSkinId(\Psr\Http\Message\UriInterface $skinId)
    {
        $this->skinId = $skinId;
    }

    /**
     * Returns the height of the thumbnail images in the release list.
     */
    public function getThumbnailHeight(): ?int
    {
        return $this->thumbnailHeight;
    }

    /**
     * Set the height of the thumbnail images in the release list.
     */
    public function setThumbnailHeight(?int $thumbnailHeight)
    {
        $this->thumbnailHeight = $thumbnailHeight;
    }

    /**
     * Returns the width of the thumbnail images in the release list.
     */
    public function getThumbnailWidth(): ?int
    {
        return $this->thumbnailWidth;
    }

    /**
     * Set the width of the thumbnail images in the release list.
     */
    public function setThumbnailWidth(?int $thumbnailWidth)
    {
        $this->thumbnailWidth = $thumbnailWidth;
    }

    /**
     * Returns the name of this object.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the name of this object.
     */
    public function setTitle(?string $title)
    {
        $this->title = $title;
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
     * Returns indicates whether to float the control rack over the media area of the player.
     */
    public function getUseFloatingControls(): ?bool
    {
        return $this->useFloatingControls;
    }

    /**
     * Set indicates whether to float the control rack over the media area of the player.
     */
    public function setUseFloatingControls(?bool $useFloatingControls)
    {
        $this->useFloatingControls = $useFloatingControls;
    }

    /**
     * Returns this object's modification version, used for optimistic locking.
     */
    public function getVersion(): ?int
    {
        return $this->version;
    }

    /**
     * Set this object's modification version, used for optimistic locking.
     */
    public function setVersion(?int $version)
    {
        $this->version = $version;
    }

    /**
     * Returns the width of the player.
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * Set the width of the player.
     */
    public function setWidth(?int $width)
    {
        $this->width = $width;
    }
}
