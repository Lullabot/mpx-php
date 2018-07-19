<?php

namespace Lullabot\Mpx\Service\Feeds;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\Feeds\FeedConfig;
use Lullabot\Mpx\DataService\Feeds\SubFeed;
use Lullabot\Mpx\DataService\PublicIdentifierInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class MediaFeedUrl
 *
 * @code
 *      http[s]://feed.media.theplatform.com/f/<account PID>/<feed PID>[/<feed type>][/feed][/<ID>][/guid/<owner ID>/<GUIDs>][/<SEO terms>][?<query parameters>]
 * @endcode
 *
 * @see https://docs.theplatform.com/help/feeds-requesting-media-feeds
 */
class MediaFeedUrl
{

    const BASE_URL = 'https://feed.media.theplatform.com/f/';

    /**
     * @var PublicIdentifierInterface
     */
    protected $account;

    /**
     * @var FeedConfig
     */
    protected $feedConfig;

    /**
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

    public function __construct(PublicIdentifierInterface $account, FeedConfig $feedConfig)
    {
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
     * @return bool
     */
    public function isReturnsFeed(): bool
    {
        return $this->returnsFeed;
    }

    /**
     * @param bool $returnsFeed
     */
    public function setReturnsFeed(bool $returnsFeed): void
    {
        $this->returnsFeed = $returnsFeed;
    }

    /**
     * @return string[]
     */
    public function getSeoTerms(): array
    {
        return $this->seoTerms;
    }

    /**
     * @param string[] $seoTerms
     */
    public function setSeoTerms(array $seoTerms): void
    {
        $this->seoTerms = $seoTerms;
    }


    public function toUri(): UriInterface
    {
        $uri = $this->uriToFeedComponent();

        $uri = $this->appendSeoTerms($uri);

        return $uri;
    }

    public function __toString()
    {
        return (string) $this->toUri();
    }

    /**
     * @return Uri
     */
    protected function uriToFeedComponent()
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
     * @param $uri
     *
     * @return mixed
     */
    protected function appendSeoTerms($uri)
    {
        if (!empty($this->seoTerms)) {
            $uri = $uri->withPath($uri->getPath().'/'.implode('/', $this->seoTerms));
        }

        return $uri;
}
}
