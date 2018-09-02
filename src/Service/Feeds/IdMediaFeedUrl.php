<?php

namespace Lullabot\Mpx\Service\Feeds;

use Lullabot\Mpx\DataService\Feeds\FeedConfig;
use Lullabot\Mpx\DataService\PublicIdentifierInterface;
use Psr\Http\Message\UriInterface;

/**
 * A feed URL with a list of items by their ID.
 */
class IdMediaFeedUrl extends MediaFeedUrl
{
    /**
     * A comma-separated list of numeric IDs for individual items in the feed.
     *
     * @var int[]
     */
    protected $ids;

    /**
     * IdMediaFeedUrl constructor.
     *
     * @param PublicIdentifierInterface $account    The account the feed is associated with.
     * @param FeedConfig                $feedConfig The feed the URL is being generated for.
     * @param array                     $ids        An array of IDs.
     */
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
     * A comma-separated list of numeric IDs for individual items in the feed.
     *
     * @param int[] $ids
     */
    public function setIds(array $ids): void
    {
        if (empty($ids)) {
            throw new \InvalidArgumentException('At least one ID must be specified');
        }

        foreach ($ids as $id) {
            if (!\is_int($id)) {
                throw new \InvalidArgumentException('All IDs must be integers');
            }
        }

        $this->ids = $ids;
    }

    /**
     * {@inheritdoc}
     */
    public function toUri(): UriInterface
    {
        $uri = $this->uriToFeedComponent();
        $uri = $uri->withPath($uri->getPath().'/'.implode(',', $this->ids));
        $uri = $this->appendSeoTerms($uri);

        return $uri;
    }
}
