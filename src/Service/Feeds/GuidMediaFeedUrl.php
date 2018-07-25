<?php

namespace Lullabot\Mpx\Service\Feeds;

use Lullabot\Mpx\DataService\Feeds\FeedConfig;
use Lullabot\Mpx\DataService\PublicIdentifierInterface;
use Psr\Http\Message\UriInterface;

/**
 * A feed URL with a list of feed items by GUID.
 */
class GuidMediaFeedUrl extends MediaFeedUrl
{
    /**
     * A comma-separated list of GUIDs for individual items in the feed.
     *
     * This path segment cannot be used with the ID path segment.
     *
     * @var string[]
     */
    protected $guids;

    /**
     * The owner ID or a dash.
     *
     * @var string
     */
    protected $ownerId;

    /**
     * GuidMediaFeedUrl constructor.
     *
     * @param PublicIdentifierInterface $account    The account the feed is associated with.
     * @param FeedConfig                $feedConfig The feed the URL is being generated for.
     * @param array                     $guids      An array of GUIDs.
     * @param string                    $ownerId    (optional) The owner ID of the associated GUIDs. Defaults to a '-' which uses the owner of the FeedConfig object.
     */
    public function __construct(PublicIdentifierInterface $account, FeedConfig $feedConfig, array $guids, string $ownerId = '-')
    {
        parent::__construct($account, $feedConfig);
        $this->setGuids($guids);
        $this->ownerId = $ownerId;
    }

    /**
     * Return the GUIDs.
     *
     * @return string[]
     */
    public function getGuids(): array
    {
        return $this->guids;
    }

    /**
     * Set either an owner ID or a dash (—).
     *
     * GUIDs are only guaranteed to be unique within an account. Because some
     * feeds can be configured to include inherited objects from other accounts,
     * it is possible that 2 objects in a feed could have the same GUID. You
     * can include the object's ownerId to uniquely identify the object.
     * Alternatively, you can include a dash (—) to specify that the owner ID is
     * the owner ID of the FeedConfig.
     *
     * @param string[] $guids
     */
    public function setGuids(array $guids): void
    {
        if (empty($guids)) {
            throw new \InvalidArgumentException('At least one GUID must be specified');
        }
        $this->guids = $guids;
    }

    /**
     * {@inheritdoc}
     */
    public function toUri(): UriInterface
    {
        $uri = $this->uriToFeedComponent();
        $uri = $uri->withPath($uri->getPath().'/guid/'.$this->ownerId.'/'.implode(',', $this->guids));
        $uri = $this->appendSeoTerms($uri);

        return $uri;
    }
}
