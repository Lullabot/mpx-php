<?php

namespace Lullabot\Mpx\Service\Feeds;

use Lullabot\Mpx\DataService\Feeds\FeedConfig;
use Lullabot\Mpx\DataService\PublicIdentifierInterface;
use Psr\Http\Message\UriInterface;

class IdMediaFeedUrl extends MediaFeedUrl
{
    /**
     * A comma-separated list of numeric IDs for individual items in the feed.
     *
     * This path segment cannot be used with the guid/<owner ID>/<GUIDs> path segment.
     *
     * @var int[]
     */
    protected $ids;

    public function __construct(PublicIdentifierInterface $account, FeedConfig $feedConfig, array $ids = [])
    {
        parent::__construct($account, $feedConfig);
        $this->setIds($ids);
    }

    /**
     * @return int[]
     */
    public function getIds(): array
    {
        return $this->ids;
    }

    /**
     * @param int[] $ids
     */
    public function setIds(array $ids): void
    {
        if (empty($ids)) {
            throw new \InvalidArgumentException('At least one ID must be specified');
        }

        $this->ids = $ids;
    }

    public function toUri(): UriInterface
    {
        $uri = $this->uriToFeedComponent();
        $uri = $uri->withPath($uri->getPath() . '/' . implode(',', $this->ids));
        $uri = $this->appendSeoTerms($uri);

        return $uri;
    }
}
