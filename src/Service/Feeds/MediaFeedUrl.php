<?php

namespace Lullabot\Mpx\Service\Feeds;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\Feeds\FeedConfig;
use Lullabot\Mpx\DataService\Feeds\SubFeed;
use Lullabot\Mpx\DataService\PublicIdentifierInterface;
use Lullabot\Mpx\ToUriInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class representing a media feed URL.
 *
 * A media feed URL follows this template:
 *
 * @code
 *      http[s]://feed.media.theplatform.com/f/<account PID>/<feed PID>[/<feed type>][/feed][/<ID>][/guid/<owner ID>/<GUIDs>][/<SEO terms>][?<query parameters>]
 * @endcode
 *
 * While mpx supports http URLs, this class only supports https URLs by default.
 * If you must return an http URL, use the withScheme() method on the returned
 * URL.
 *
 * Note that IDs and GUIDs can not be specified in the same URL.
 *
 * @see https://docs.theplatform.com/help/feeds-requesting-media-feeds
 * @see IdMediaFeedUrl
 * @see GuidMediaFeedUrl
 */
class MediaFeedUrl implements ToUriInterface
{
    /**
     * The base URL for all feed requests.
     */
    const BASE_URL = 'https://feed.media.theplatform.com/f/';

    /**
     * The account the feed is associated with.
     *
     * @var PublicIdentifierInterface
     */
    protected $account;

    /**
     * The feed being rendered.
     *
     * @var PublicIdentifierInterface
     */
    protected $feedConfig;

    /**
     * A subfeed specifying the feed type.
     *
     * @var SubFeed
     */
    protected $feedTypeSubFeed;

    /**
     * Forces the response to be in feed format.
     *
     * @var bool
     */
    protected $returnsFeed = false;

    /**
     * A list of SEO terms for the feed.
     *
     * @var string[]
     */
    protected $seoTerms = [];

    /**
     * MediaFeedUrl constructor.
     *
     * @param PublicIdentifierInterface $account    The account the feed is associated with.
     * @param PublicIdentifierInterface $feedConfig The feed the URL is being generated for.
     */
    public function __construct(PublicIdentifierInterface $account, PublicIdentifierInterface $feedConfig)
    {
        if (empty($account->getPid())) {
            throw new \InvalidArgumentException('Account must have a public identifier set');
        }
        if (empty($feedConfig->getPid())) {
            throw new \InvalidArgumentException('Feed config must have a public identifier set');
        }

        $this->account = $account;
        $this->feedConfig = $feedConfig;
    }

    /**
     * Get the subfeed that specifies the feed type.
     *
     * @return SubFeed
     */
    public function getFeedType(): ?SubFeed
    {
        return $this->feedTypeSubFeed;
    }

    /**
     * Specifies a subfeed of the main feed.
     *
     * This corresponds to a SubFeed.FeedType value of an item in the FeedConfig.subFeeds field.
     *
     * @param SubFeed $subFeed
     */
    public function setFeedType(?SubFeed $subFeed): void
    {
        if ($subFeed && !$subFeed->getFeedType()) {
            throw new \InvalidArgumentException('The feedType field must be specified on the subfeed.');
        }
        $this->feedTypeSubFeed = $subFeed;
    }

    /**
     * Returns if this URL requests a feed response always, even if only one
     * item is in the feed.
     */
    public function isReturnsFeed(): bool
    {
        return $this->returnsFeed;
    }

    /**
     * Set if this URL requests a feed response always, even if only one
     * item is in the feed.
     */
    public function setReturnsFeed(bool $returnsFeed): void
    {
        $this->returnsFeed = $returnsFeed;
    }

    /**
     * Return the array of SEO terms.
     *
     * @return string[]
     */
    public function getSeoTerms(): array
    {
        return $this->seoTerms;
    }

    /**
     * Set an array of SEO terms.
     *
     * Terms with a forward slash will also be considered as separate terms.
     *
     * @param string[] $seoTerms
     */
    public function setSeoTerms(array $seoTerms): void
    {
        $this->seoTerms = $seoTerms;
    }

    /**
     * {@inheritdoc}
     */
    public function toUri(): UriInterface
    {
        $uri = $this->uriToFeedComponent();
        $uri = $this->appendSeoTerms($uri);

        return $uri;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->toUri();
    }

    /**
     * Return a new URI with all components up to the '/feed' component.
     *
     * @return Uri The new URI.
     */
    protected function uriToFeedComponent(): Uri
    {
        $uri = new Uri(static::BASE_URL.$this->account->getPid().'/'.$this->feedConfig->getPid());

        if ($this->feedTypeSubFeed) {
            $uri = $uri->withPath($uri->getPath().'/'.$this->feedTypeSubFeed->getFeedType());
        }

        if ($this->isReturnsFeed()) {
            $uri = $uri->withPath($uri->getPath().'/feed');
        }

        return $uri;
    }

    /**
     * Append the SEO terms to the end of a URI.
     *
     * @param UriInterface $uri The URI to append to.
     */
    protected function appendSeoTerms(UriInterface $uri): UriInterface
    {
        if (!empty($this->seoTerms)) {
            $uri = $uri->withPath($uri->getPath().'/'.implode('/', $this->seoTerms));
        }

        return $uri;
    }
}
