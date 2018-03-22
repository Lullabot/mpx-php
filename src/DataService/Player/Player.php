<?php

namespace Lullabot\Mpx\DataService\Player;

use Lullabot\Mpx\CreateKeyInterface;
use Lullabot\Mpx\DataService\BaseDataTrait;
use Psr\Http\Message\UriInterface;

/**
 * @\Lullabot\Mpx\DataService\Annotation\DataService(
 *     service="Player Data Service",
 *     path="/data/Player",
 *     schemaVersion="1.6",
 * )
 */
class Player implements CreateKeyInterface
{
    use BaseDataTrait;

    /**
     * The administrative workflow tags for this object.
     *
     * @var string[]
     */
    protected $adminTags;

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
    protected $customCssUrls;

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
     * @var \Psr\Http\Message\UriInterface[]
     */
    protected $customJavaScriptUrls;

    /**
     * Additional attributes to include in the player HTML.
     *
     * @var array<string, string>
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
     * @var PlugInInstance[]
     */
    protected $enabledPlugIns;

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
    protected $limitToCategories;

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
    protected $preferredFormats;

    /**
     * The runtimes meant for use in the player.
     *
     * @var string[]
     */
    protected $preferredRuntimes;

    /**
     * Heights of the regions in the layout.
     *
     * @var array<string, Integer>
     */
    protected $regionHeights;

    /**
     * Widths of the regions in the layout.
     *
     * @var array<string, Integer>
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
     * Returns the identifier for the advertising policy for this object.
     *
     * @return UriInterface
     */
    public function getAdPolicyId(): UriInterface
    {
        return $this->adPolicyId;
    }

    /**
     * Set the identifier for the advertising policy for this object.
     *
     * @param UriInterface
     */
    public function setAdPolicyId($adPolicyId)
    {
        $this->adPolicyId = $this->convertUri($adPolicyId);
    }

    /**
     * Returns Indicates whether the player will feature sharing via email.
     *
     * @return bool
     */
    public function getAllowEmail(): bool
    {
        return $this->allowEmail;
    }

    /**
     * Set Indicates whether the player will feature sharing via email.
     *
     * @param bool
     */
    public function setAllowEmail($allowEmail)
    {
        $this->allowEmail = $allowEmail;
    }

    /**
     * Returns Indicates whether the player will provide an embed code for sharing on other sites.
     *
     * @return bool
     */
    public function getAllowEmbed(): bool
    {
        return $this->allowEmbed;
    }

    /**
     * Set Indicates whether the player will provide an embed code for sharing on other sites.
     *
     * @param bool
     */
    public function setAllowEmbed($allowEmbed)
    {
        $this->allowEmbed = $allowEmbed;
    }

    /**
     * Returns Indicates whether the player will be allowed to play video in fullscreen mode.
     *
     * @return bool
     */
    public function getAllowFullScreen(): bool
    {
        return $this->allowFullScreen;
    }

    /**
     * Set Indicates whether the player will be allowed to play video in fullscreen mode.
     *
     * @param bool
     */
    public function setAllowFullScreen($allowFullScreen)
    {
        $this->allowFullScreen = $allowFullScreen;
    }

    /**
     * Returns Indicates whether the player will make a shareable link available to users.
     *
     * @return bool
     */
    public function getAllowGetLink(): bool
    {
        return $this->allowGetLink;
    }

    /**
     * Set Indicates whether the player will make a shareable link available to users.
     *
     * @param bool
     */
    public function setAllowGetLink($allowGetLink)
    {
        $this->allowGetLink = $allowGetLink;
    }

    /**
     * Returns Indicates whether the player will make an RSS link available to users.
     *
     * @return bool
     */
    public function getAllowRss(): bool
    {
        return $this->allowRss;
    }

    /**
     * Set Indicates whether the player will make an RSS link available to users.
     *
     * @param bool
     */
    public function setAllowRss($allowRss)
    {
        $this->allowRss = $allowRss;
    }

    /**
     * Returns Indicates whether to include the search component.
     *
     * @return bool
     */
    public function getAllowSearch(): bool
    {
        return $this->allowSearch;
    }

    /**
     * Set Indicates whether to include the search component.
     *
     * @param bool
     */
    public function setAllowSearch($allowSearch)
    {
        $this->allowSearch = $allowSearch;
    }

    /**
     * Returns Indicates whether the player should always display the play overlay.
     *
     * @return bool
     */
    public function getAlwaysShowOverlay(): bool
    {
        return $this->alwaysShowOverlay;
    }

    /**
     * Set Indicates whether the player should always display the play overlay.
     *
     * @param bool
     */
    public function setAlwaysShowOverlay($alwaysShowOverlay)
    {
        $this->alwaysShowOverlay = $alwaysShowOverlay;
    }

    /**
     * Returns the value of the aspect ratio for the height of the media area.
     *
     * @return int
     */
    public function getAspectRatioHeight(): int
    {
        return $this->aspectRatioHeight;
    }

    /**
     * Set the value of the aspect ratio for the height of the media area.
     *
     * @param int
     */
    public function setAspectRatioHeight($aspectRatioHeight)
    {
        $this->aspectRatioHeight = $aspectRatioHeight;
    }

    /**
     * Returns the value of the aspect ratio for the width of the media area.
     *
     * @return int
     */
    public function getAspectRatioWidth(): int
    {
        return $this->aspectRatioWidth;
    }

    /**
     * Set the value of the aspect ratio for the width of the media area.
     *
     * @param int
     */
    public function setAspectRatioWidth($aspectRatioWidth)
    {
        $this->aspectRatioWidth = $aspectRatioWidth;
    }

    /**
     * Returns Indicates whether the player will start playback on load.
     *
     * @return bool
     */
    public function getAutoPlay(): bool
    {
        return $this->autoPlay;
    }

    /**
     * Set Indicates whether the player will start playback on load.
     *
     * @param bool
     */
    public function setAutoPlay($autoPlay)
    {
        $this->autoPlay = $autoPlay;
    }

    /**
     * Returns Indicates whether the player will self-initialize on load.
     *
     * @return bool
     */
    public function getAutoInitialize(): bool
    {
        return $this->autoInitialize;
    }

    /**
     * Set Indicates whether the player will self-initialize on load.
     *
     * @param bool
     */
    public function setAutoInitialize($autoInitialize)
    {
        $this->autoInitialize = $autoInitialize;
    }

    /**
     * Returns URL for a custom background image.
     *
     * @return string
     */
    public function getBackgroundImageUrl(): string
    {
        return $this->backgroundImageUrl;
    }

    /**
     * Set URL for a custom background image.
     *
     * @param string
     */
    public function setBackgroundImageUrl($backgroundImageUrl)
    {
        $this->backgroundImageUrl = $this->convertUri($backgroundImageUrl);
    }

    /**
     * Returns Identifier for the color scheme assigned to this player.
     *
     * @return UriInterface
     */
    public function getColorSchemeId(): UriInterface
    {
        return $this->colorSchemeId;
    }

    /**
     * Set Identifier for the color scheme assigned to this player.
     *
     * @param UriInterface
     */
    public function setColorSchemeId($colorSchemeId)
    {
        $this->colorSchemeId = $this->convertUri($colorSchemeId);
    }

    /**
     * Returns the number of columns in the release list.
     *
     * @return int
     */
    public function getColumns(): int
    {
        return $this->columns;
    }

    /**
     * Set the number of columns in the release list.
     *
     * @param int
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * Returns Reserved for future use. The default value is false.
     *
     * @return bool
     */
    public function getCompatibilityMode(): bool
    {
        return $this->compatibilityMode;
    }

    /**
     * Set Reserved for future use. The default value is false.
     *
     * @param bool
     */
    public function setCompatibilityMode($compatibilityMode)
    {
        $this->compatibilityMode = $compatibilityMode;
    }

    /**
     * Returns XML layout content for the player control rack.
     *
     * @return string
     */
    public function getControlLayoutXml(): string
    {
        return $this->controlLayoutXml;
    }

    /**
     * Set XML layout content for the player control rack.
     *
     * @param string
     */
    public function setControlLayoutXml($controlLayoutXml)
    {
        $this->controlLayoutXml = $controlLayoutXml;
    }

    /**
     * Returns Height of the player control rack.
     *
     * @return int
     */
    public function getControlRackHeight(): int
    {
        return $this->controlRackHeight;
    }

    /**
     * Set Height of the player control rack.
     *
     * @param int
     */
    public function setControlRackHeight($controlRackHeight)
    {
        $this->controlRackHeight = $controlRackHeight;
    }

    /**
     * Returns Custom CSS content for the player.
     *
     * @return string
     */
    public function getCustomCss(): string
    {
        return $this->customCss;
    }

    /**
     * Set Custom CSS content for the player.
     *
     * @param string
     */
    public function setCustomCss($customCss)
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
     * @param string[]
     */
    public function setCustomCssUrls($customCssUrls)
    {
        $this->customCssUrls = $customCssUrls;
    }

    /**
     * Returns Custom HTML content for the player (not currently used).
     *
     * @return string
     */
    public function getCustomHtml(): string
    {
        return $this->customHtml;
    }

    /**
     * Set Custom HTML content for the player (not currently used).
     *
     * @param string
     */
    public function setCustomHtml($customHtml)
    {
        $this->customHtml = $customHtml;
    }

    /**
     * Returns Custom JavaScript content for the player.
     *
     * @return string
     */
    public function getCustomJavaScript(): string
    {
        return $this->customJavaScript;
    }

    /**
     * Set Custom JavaScript content for the player.
     *
     * @param string
     */
    public function setCustomJavaScript($customJavaScript)
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
     * @param string[]
     */
    public function setCustomJavaScriptUrls($customJavaScriptUrls)
    {
        $this->customJavaScriptUrls = $customJavaScriptUrls;
    }

    /**
     * Returns Additional attributes to include in the player HTML.
     *
     * @return string[]
     */
    public function getCustomProperties(): array
    {
        return $this->customProperties;
    }

    /**
     * Set Additional attributes to include in the player HTML.
     *
     * @param array<string, string>
     */
    public function setCustomProperties($customProperties)
    {
        $this->customProperties = $customProperties;
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
     * Returns Whether is player is available for customer retrieval.
     *
     * @return bool
     */
    public function getDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * Set Whether is player is available for customer retrieval.
     *
     * @param bool
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    }

    /**
     * Returns the identifier for the advertising policy to use when the player is embedded in another site.
     *
     * @return UriInterface
     */
    public function getEmbedAdPolicyId(): UriInterface
    {
        return $this->embedAdPolicyId;
    }

    /**
     * Set the identifier for the advertising policy to use when the player is embedded in another site.
     *
     * @param UriInterface
     */
    public function setEmbedAdPolicyId($embedAdPolicyId)
    {
        $this->embedAdPolicyId = $this->convertUri($embedAdPolicyId);
    }

    /**
     * Returns Indicates whether embedded players will be allowed to be viewed in fullscreen mode.
     *
     * @return bool
     */
    public function getEmbedAllowFullScreen(): bool
    {
        return $this->embedAllowFullScreen;
    }

    /**
     * Set Indicates whether embedded players will be allowed to be viewed in fullscreen mode.
     *
     * @param bool
     */
    public function setEmbedAllowFullScreen($embedAllowFullScreen)
    {
        $this->embedAllowFullScreen = $embedAllowFullScreen;
    }

    /**
     * Returns the default height of the player when embedded in another site.
     *
     * @return int
     */
    public function getEmbedHeight(): int
    {
        return $this->embedHeight;
    }

    /**
     * Set the default height of the player when embedded in another site.
     *
     * @param int
     */
    public function setEmbedHeight($embedHeight)
    {
        $this->embedHeight = $embedHeight;
    }

    /**
     * Returns the identifier for the restriction to apply to this player when embedded in another site.
     *
     * @return UriInterface
     */
    public function getEmbedRestrictionId(): UriInterface
    {
        return $this->embedRestrictionId;
    }

    /**
     * Set the identifier for the restriction to apply to this player when embedded in another site.
     *
     * @param UriInterface
     */
    public function setEmbedRestrictionId($embedRestrictionId)
    {
        $this->embedRestrictionId = $this->convertUri($embedRestrictionId);
    }

    /**
     * Returns the default width of the player when embedded in another site.
     *
     * @return int
     */
    public function getEmbedWidth(): int
    {
        return $this->embedWidth;
    }

    /**
     * Set the default width of the player when embedded in another site.
     *
     * @param int
     */
    public function setEmbedWidth($embedWidth)
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
     * @param PlugInInstance[]
     */
    public function setEnabledPlugIns($enabledPlugIns)
    {
        $this->enabledPlugIns = $enabledPlugIns;
    }

    /**
     * Returns Indicates if the player responds to commands from an IFrame parent element.
     *
     * @return bool
     */
    public function getEnableExternalController(): bool
    {
        return $this->enableExternalController;
    }

    /**
     * Set Indicates if the player responds to commands from an IFrame parent element.
     *
     * @param bool
     */
    public function setEnableExternalController($enableExternalController)
    {
        $this->enableExternalController = $enableExternalController;
    }

    /**
     * Returns the URL of the feed that will populate the related items list end card.
     *
     * @return string
     */
    public function getEndCardFeedUrl(): string
    {
        return $this->endCardFeedUrl;
    }

    /**
     * Set the URL of the feed that will populate the related items list end card.
     *
     * @param string
     */
    public function setEndCardFeedUrl($endCardFeedUrl)
    {
        $this->endCardFeedUrl = $this->convertUri($endCardFeedUrl);
    }

    /**
     * Returns the custom failover HTML.
     *
     * @return string
     */
    public function getFailoverHtml(): string
    {
        return $this->failoverHtml;
    }

    /**
     * Set the custom failover HTML.
     *
     * @param string
     */
    public function setFailoverHtml($failoverHtml)
    {
        $this->failoverHtml = $failoverHtml;
    }

    /**
     * Returns Reserved for future use. The default value is null.
     *
     * @return string
     */
    public function getFallbackPdk(): string
    {
        return $this->fallbackPdk;
    }

    /**
     * Set Reserved for future use. The default value is null.
     *
     * @param string
     */
    public function setFallbackPdk($fallbackPdk)
    {
        $this->fallbackPdk = $fallbackPdk;
    }

    /**
     * Returns the URL of the player's default feed.
     *
     * @return string
     */
    public function getFeedUrl(): string
    {
        return $this->feedUrl;
    }

    /**
     * Set the URL of the player's default feed.
     *
     * @param string
     */
    public function setFeedUrl($feedUrl)
    {
        $this->feedUrl = $this->convertUri($feedUrl);
    }

    /**
     * Returns the request parameters to include in the feed request.
     *
     * @return string
     */
    public function getFeedUrlParams(): string
    {
        return $this->feedUrlParams;
    }

    /**
     * Set the request parameters to include in the feed request.
     *
     * @param string
     */
    public function setFeedUrlParams($feedUrlParams)
    {
        $this->feedUrlParams = $feedUrlParams;
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
     * Returns the height of the header image.
     *
     * @return int
     */
    public function getHeaderImageHeight(): int
    {
        return $this->headerImageHeight;
    }

    /**
     * Set the height of the header image.
     *
     * @param int
     */
    public function setHeaderImageHeight($headerImageHeight)
    {
        $this->headerImageHeight = $headerImageHeight;
    }

    /**
     * Returns the URL of the header image.
     *
     * @return string
     */
    public function getHeaderImageUrl(): string
    {
        return $this->headerImageUrl;
    }

    /**
     * Set the URL of the header image.
     *
     * @param string
     */
    public function setHeaderImageUrl($headerImageUrl)
    {
        $this->headerImageUrl = $this->convertUri($headerImageUrl);
    }

    /**
     * Returns the height of the player.
     *
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Set the height of the player.
     *
     * @param int
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * Returns Whether to include the default player CSS file in the player HTML page.
     *
     * @return bool
     */
    public function getIncludeDefaultCss(): bool
    {
        return $this->includeDefaultCss;
    }

    /**
     * Set Whether to include the default player CSS file in the player HTML page.
     *
     * @param bool
     */
    public function setIncludeDefaultCss($includeDefaultCss)
    {
        $this->includeDefaultCss = $includeDefaultCss;
    }

    /**
     * Returns the number of items to make visible in each page of the release list.
     *
     * @return int
     */
    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    /**
     * Set the number of items to make visible in each page of the release list.
     *
     * @param int
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * Returns the identifier for the layout assigned to this player.
     *
     * @return UriInterface
     */
    public function getLayoutId(): UriInterface
    {
        return $this->layoutId;
    }

    /**
     * Set the identifier for the layout assigned to this player.
     *
     * @param UriInterface
     */
    public function setLayoutId($layoutId)
    {
        $this->layoutId = $this->convertUri($layoutId);
    }

    /**
     * Returns A list of categories that will appear in the category list.
     *
     * @return string[]
     */
    public function getLimitToCategories(): array
    {
        return $this->limitToCategories;
    }

    /**
     * Set A list of categories that will appear in the category list.
     *
     * @param string[]
     */
    public function setLimitToCategories($limitToCategories)
    {
        $this->limitToCategories = $limitToCategories;
    }

    /**
     * Returns the destination URL for when a user clicks the player video area.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getLinkUrl(): UriInterface
    {
        return $this->linkUrl;
    }

    /**
     * Set the destination URL for when a user clicks the player video area.
     *
     * @param string
     */
    public function setLinkUrl($linkUrl)
    {
        $this->linkUrl = $this->convertUri($linkUrl);
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
     * Returns the URL of the player overlay image.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getOverlayImageUrl(): UriInterface
    {
        return $this->overlayImageUrl;
    }

    /**
     * Set the URL of the player overlay image.
     *
     * @param string
     */
    public function setOverlayImageUrl($overlayImageUrl)
    {
        $this->overlayImageUrl = $this->convertUri($overlayImageUrl);
    }

    /**
     * Returns the amount of padding to add to the bottom of the player host page.
     *
     * @return int
     */
    public function getPaddingBottom(): int
    {
        return $this->paddingBottom;
    }

    /**
     * Set the amount of padding to add to the bottom of the player host page.
     *
     * @param int
     */
    public function setPaddingBottom($paddingBottom)
    {
        $this->paddingBottom = $paddingBottom;
    }

    /**
     * Returns the amount of padding to add to the left side of the player host page.
     *
     * @return int
     */
    public function getPaddingLeft(): int
    {
        return $this->paddingLeft;
    }

    /**
     * Set the amount of padding to add to the left side of the player host page.
     *
     * @param int
     */
    public function setPaddingLeft($paddingLeft)
    {
        $this->paddingLeft = $paddingLeft;
    }

    /**
     * Returns the amount of padding to add to the right side of the player host page.
     *
     * @return int
     */
    public function getPaddingRight(): int
    {
        return $this->paddingRight;
    }

    /**
     * Set the amount of padding to add to the right side of the player host page.
     *
     * @param int
     */
    public function setPaddingRight($paddingRight)
    {
        $this->paddingRight = $paddingRight;
    }

    /**
     * Returns the amount of padding to add to the top of the player host page.
     *
     * @return int
     */
    public function getPaddingTop(): int
    {
        return $this->paddingTop;
    }

    /**
     * Set the amount of padding to add to the top of the player host page.
     *
     * @param int
     */
    public function setPaddingTop($paddingTop)
    {
        $this->paddingTop = $paddingTop;
    }

    /**
     * Returns Reserved for future use. The default value is null.
     *
     * @return string
     */
    public function getPdk(): string
    {
        return $this->pdk;
    }

    /**
     * Set Reserved for future use. The default value is null.
     *
     * @param string
     */
    public function setPdk($pdk)
    {
        $this->pdk = $pdk;
    }

    /**
     * Returns the public identifier for this player when requested through the Player Service.
     *
     * @return string
     */
    public function getPid(): string
    {
        return $this->pid;
    }

    /**
     * Set the public identifier for this player when requested through the Player Service.
     *
     * @param string
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    /**
     * Returns Indicates if the player should automatically play the next release when one finishes.
     *
     * @return bool
     */
    public function getPlayAll(): bool
    {
        return $this->playAll;
    }

    /**
     * Set Indicates if the player should automatically play the next release when one finishes.
     *
     * @param bool
     */
    public function setPlayAll($playAll)
    {
        $this->playAll = $playAll;
    }

    /**
     * Returns Player URL used in the sharing features.
     *
     * @return string
     */
    public function getPlayerUrl(): string
    {
        return $this->playerUrl;
    }

    /**
     * Set Player URL used in the sharing features.
     *
     * @param string
     */
    public function setPlayerUrl($playerUrl)
    {
        $this->playerUrl = $playerUrl;
    }

    /**
     * Returns the default height to use for the poster image.
     *
     * @return int
     */
    public function getPosterImageDefaultHeight(): int
    {
        return $this->posterImageDefaultHeight;
    }

    /**
     * Set the default height to use for the poster image.
     *
     * @param int
     */
    public function setPosterImageDefaultHeight($posterImageDefaultHeight)
    {
        $this->posterImageDefaultHeight = $posterImageDefaultHeight;
    }

    /**
     * Returns the default width to use for the poster image.
     *
     * @return int
     */
    public function getPosterImageDefaultWidth(): int
    {
        return $this->posterImageDefaultWidth;
    }

    /**
     * Set the default width to use for the poster image.
     *
     * @param int
     */
    public function setPosterImageDefaultWidth($posterImageDefaultWidth)
    {
        $this->posterImageDefaultWidth = $posterImageDefaultWidth;
    }

    /**
     * Returns the meta asset type to use for the poster image.
     *
     * @return string
     */
    public function getPosterImageMetaAssetType(): string
    {
        return $this->posterImageMetaAssetType;
    }

    /**
     * Set the meta asset type to use for the poster image.
     *
     * @param string
     */
    public function setPosterImageMetaAssetType($posterImageMetaAssetType)
    {
        $this->posterImageMetaAssetType = $posterImageMetaAssetType;
    }

    /**
     * Returns the preview asset type to use for the poster image.
     *
     * @return string
     */
    public function getPosterImagePreviewAssetType(): string
    {
        return $this->posterImagePreviewAssetType;
    }

    /**
     * Set the preview asset type to use for the poster image.
     *
     * @param string
     */
    public function setPosterImagePreviewAssetType($posterImagePreviewAssetType)
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
     * @param string[]
     */
    public function setPreferredFormats($preferredFormats)
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
     * @param string[]
     */
    public function setPreferredRuntimes($preferredRuntimes)
    {
        $this->preferredRuntimes = $preferredRuntimes;
    }

    /**
     * Returns Heights of the regions in the layout.
     *
     * @return int[]
     */
    public function getRegionHeights(): array
    {
        return $this->regionHeights;
    }

    /**
     * Set Heights of the regions in the layout.
     *
     * @param array<string, Integer>
     */
    public function setRegionHeights($regionHeights)
    {
        $this->regionHeights = $regionHeights;
    }

    /**
     * Returns Widths of the regions in the layout.
     *
     * @return int[]
     */
    public function getRegionWidths(): array
    {
        return $this->regionWidths;
    }

    /**
     * Set Widths of the regions in the layout.
     *
     * @param array<string, Integer>
     */
    public function setRegionWidths($regionWidths)
    {
        $this->regionWidths = $regionWidths;
    }

    /**
     * Returns URL parameters to add to Public URL requests.
     *
     * @return string
     */
    public function getReleaseUrlParams(): string
    {
        return $this->releaseUrlParams;
    }

    /**
     * Set URL parameters to add to Public URL requests.
     *
     * @param string
     */
    public function setReleaseUrlParams($releaseUrlParams)
    {
        $this->releaseUrlParams = $releaseUrlParams;
    }

    /**
     * Returns the identifier of the restriction to apply to this player.
     *
     * @return UriInterface
     */
    public function getRestrictionId(): UriInterface
    {
        return $this->restrictionId;
    }

    /**
     * Set the identifier of the restriction to apply to this player.
     *
     * @param UriInterface
     */
    public function setRestrictionId($restrictionId)
    {
        $this->restrictionId = $this->convertUri($restrictionId);
    }

    /**
     * Returns Indicates whether to display the air date in the release list component.
     *
     * @return bool
     */
    public function getShowAirdate(): bool
    {
        return $this->showAirdate;
    }

    /**
     * Set Indicates whether to display the air date in the release list component.
     *
     * @param bool
     */
    public function setShowAirdate($showAirdate)
    {
        $this->showAirdate = $showAirdate;
    }

    /**
     * Returns Indicates whether to include the All option in the category list component.
     *
     * @return bool
     */
    public function getShowAllChoice(): bool
    {
        return $this->showAllChoice;
    }

    /**
     * Set Indicates whether to include the All option in the category list component.
     *
     * @param bool
     */
    public function setShowAllChoice($showAllChoice)
    {
        $this->showAllChoice = $showAllChoice;
    }

    /**
     * Returns Indicates whether to display the author in the release list and clip info components.
     *
     * @return bool
     */
    public function getShowAuthor(): bool
    {
        return $this->showAuthor;
    }

    /**
     * Set Indicates whether to display the author in the release list and clip info components.
     *
     * @param bool
     */
    public function setShowAuthor($showAuthor)
    {
        $this->showAuthor = $showAuthor;
    }

    /**
     * Returns Indicates whether to display the media bitrate in the release list component.
     *
     * @return bool
     */
    public function getShowBitrate(): bool
    {
        return $this->showBitrate;
    }

    /**
     * Set Indicates whether to display the media bitrate in the release list component.
     *
     * @param bool
     */
    public function setShowBitrate($showBitrate)
    {
        $this->showBitrate = $showBitrate;
    }

    /**
     * Returns Indicates whether to show the full video time in the player control rack.
     *
     * @return bool
     */
    public function getShowFullTime(): bool
    {
        return $this->showFullTime;
    }

    /**
     * Set Indicates whether to show the full video time in the player control rack.
     *
     * @param bool
     */
    public function setShowFullTime($showFullTime)
    {
        $this->showFullTime = $showFullTime;
    }

    /**
     * Returns Indicates whether to show the Most Popular option in the category list component.
     *
     * @return bool
     */
    public function getShowMostPopularChoice(): bool
    {
        return $this->showMostPopularChoice;
    }

    /**
     * Set Indicates whether to show the Most Popular option in the category list component.
     *
     * @param bool
     */
    public function setShowMostPopularChoice($showMostPopularChoice)
    {
        $this->showMostPopularChoice = $showMostPopularChoice;
    }

    /**
     * Returns Indicates whether to display the previous and next buttons in the player control rack.
     *
     * @return bool
     */
    public function getShowNav(): bool
    {
        return $this->showNav;
    }

    /**
     * Set Indicates whether to display the previous and next buttons in the player control rack.
     *
     * @param bool
     */
    public function setShowNav($showNav)
    {
        $this->showNav = $showNav;
    }

    /**
     * Returns Indicates whether to randomize the contents of the release list.
     *
     * @return bool
     */
    public function getShuffle(): bool
    {
        return $this->shuffle;
    }

    /**
     * Set Indicates whether to randomize the contents of the release list.
     *
     * @param bool
     */
    public function setShuffle($shuffle)
    {
        $this->shuffle = $shuffle;
    }

    /**
     * Returns Identifier for the skin object to apply this player.
     *
     * @return UriInterface
     */
    public function getSkinId(): UriInterface
    {
        return $this->skinId;
    }

    /**
     * Set Identifier for the skin object to apply this player.
     *
     * @param UriInterface
     */
    public function setSkinId($skinId)
    {
        $this->skinId = $this->convertUri($skinId);
    }

    /**
     * Returns the height of the thumbnail images in the release list.
     *
     * @return int
     */
    public function getThumbnailHeight(): int
    {
        return $this->thumbnailHeight;
    }

    /**
     * Set the height of the thumbnail images in the release list.
     *
     * @param int
     */
    public function setThumbnailHeight($thumbnailHeight)
    {
        $this->thumbnailHeight = $thumbnailHeight;
    }

    /**
     * Returns the width of the thumbnail images in the release list.
     *
     * @return int
     */
    public function getThumbnailWidth(): int
    {
        return $this->thumbnailWidth;
    }

    /**
     * Set the width of the thumbnail images in the release list.
     *
     * @param int
     */
    public function setThumbnailWidth($thumbnailWidth)
    {
        $this->thumbnailWidth = $thumbnailWidth;
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
     * @param int
     */
    public function setUpdated($updated)
    {
        $this->updated = $this->convertDateTime($updated);
    }

    /**
     * Returns the id of the user that last modified this object.
     *
     * @return UriInterface
     */
    public function getUpdatedByUserId(): UriInterface
    {
        return $this->updatedByUserId;
    }

    /**
     * Set the id of the user that last modified this object.
     *
     * @param UriInterface
     */
    public function setUpdatedByUserId($updatedByUserId)
    {
        $this->updatedByUserId = $this->convertUri($updatedByUserId);
    }

    /**
     * Returns Indicates whether to float the control rack over the media area of the player.
     *
     * @return bool
     */
    public function getUseFloatingControls(): bool
    {
        return $this->useFloatingControls;
    }

    /**
     * Set Indicates whether to float the control rack over the media area of the player.
     *
     * @param bool
     */
    public function setUseFloatingControls($useFloatingControls)
    {
        $this->useFloatingControls = $useFloatingControls;
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
     * Returns the width of the player.
     *
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Set the width of the player.
     *
     * @param int
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Returns the name of the field containing the canonical identifier, such as 'id'.
     *
     * @return string The ID key.
     */
    public function getIdKey(): string
    {
        return 'id';
    }

    /**
     * Returns an array of all defined compound keys.
     *
     * For example, a Media object would return @code [['ownerId', 'guid']] @endcode.
     *
     * @see https://docs.theplatform.com/help/media-media-object
     *
     * @return array[] An array of compound keys, each compound key as an array.
     */
    public function getCompoundKeys(): array
    {
        return [];
    }

    /**
     * Returns an array of all custom keys.
     *
     * Typically, such keys are created by setting isUnique on the field.
     *
     * @see https://docs.theplatform.com/help/wsf-working-with-custom-fields#Workingwithcustomfields-Uniquecustomvalues
     *
     * @return string[] An array of custom keys.
     */
    public function getCustomKeys(): array
    {
        // TODO: Implement getCustomKeys() method.
    }
}
