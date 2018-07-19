<?php

namespace Lullabot\Mpx\Service\Feeds;

use Lullabot\Mpx\DataService\Feeds\FeedConfig;
use Lullabot\Mpx\DataService\PublicIdentifierInterface;
use Psr\Http\Message\UriInterface;

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
     * Contains either an owner ID or a dash (—).
     *
     * GUIDs are only guaranteed to be unique within an account. Because some
     * feeds can be configured to include inherited objects from other accounts,
     * it is possible that 2 objects in a feed could have the same GUID. You
     * can include the object's ownerId to uniquely identify the object.
     * Alternatively, you can include a dash (—) to specify that the owner ID is
     * the owner ID of the FeedConfig.
     *
     * @var string
     */
    protected $ownerId;

    public function __construct(PublicIdentifierInterface $account, FeedConfig $feedConfig, array $guids, string $ownerId = '-')
    {
        parent::__construct($account, $feedConfig);
        $this->setGuids($guids);
        $this->ownerId = $ownerId;
    }

    /**
     * @return string[]
     */
    public function getGuids(): array
    {
        return $this->guids;
    }

    /**
     * @param string[] $guids
     */
    public function setGuids(array $guids): void
    {
        if (empty($guids)) {
            throw new \InvalidArgumentException('At least one GUID must be specified');
        }
        $this->guids = $guids;
    }

    public function toUri(): UriInterface
    {
        $uri = $this->uriToFeedComponent();
        $uri = $uri->withPath($uri->getPath() . '/guid/' . implode(',', $this->guids));
        $uri = $this->appendSeoTerms($uri);

        return $uri;
    }
}
