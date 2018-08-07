<?php

namespace Lullabot\Mpx\Service\Player;

use function GuzzleHttp\Psr7\build_query;
use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\Media\Media;
use Lullabot\Mpx\DataService\Player\Player;
use Lullabot\Mpx\DataService\PublicIdentifierInterface;
use Lullabot\Mpx\ToUriInterface;
use Psr\Http\Message\UriInterface;

/**
 * Represents a player URL, suitable for embedding with an iframe.
 *
 * @see https://docs.theplatform.com/help/displaying-mpx-players-to-your-audience
 * @see https://docs.theplatform.com/help/generate-a-player-url-for-a-media
 */
class Url implements ToUriInterface
{
    /**
     * The base URL for all players.
     */
    const BASE_URL = 'https://player.theplatform.com/p/';

    /**
     * The account the player belongs to.
     *
     * @var \Lullabot\Mpx\DataService\PublicIdentifierInterface
     */
    private $account;

    /**
     * The player object the URL is being generated for.
     *
     * @var Player
     */
    private $player;

    /**
     * The media that is being played.
     *
     * @var Media
     */
    private $media;

    /**
     * Should autoplay be overridden?
     *
     * @var bool
     */
    private $autoplay;

    /**
     * Should the playAll setting be overridden?
     *
     * @var bool
     */
    private $playAll;

    /**
     * Url constructor.
     *
     * @param PublicIdentifierInterface $account The account the player is owned by.
     * @param Player                    $player  The player to play $media with.
     * @param Media                     $media   The media to play.
     */
    public function __construct(PublicIdentifierInterface $account, Player $player, Media $media)
    {
        $this->player = $player;
        $this->media = $media;
        $this->account = $account;
    }

    /**
     * Return the URL for this player and media.
     *
     * @return UriInterface
     */
    public function toUri(): UriInterface
    {
        $uri = new Uri($this::BASE_URL.$this->account->getPid().'/'.$this->player->getPid().'/select/media/'.$this->media->getPid());
        $query_parts = [];

        if ($this->autoplay) {
            $query_parts['autoplay'] = $this->autoplay;
        }
        if ($this->playAll) {
            $query_parts['playAll'] = $this->playAll;
        }

        $uri = $uri->withQuery(build_query($query_parts));

        return $uri;
    }

    /**
     * Returns the URL of this player as a string.
     *
     * @return string The player URL.
     */
    public function __toString()
    {
        return (string) $this->toUri();
    }

    /**
     * Override the player's autoplay setting for this URL.
     *
     * @see https://docs.theplatform.com/help/player-player-autoplay
     *
     * @param bool $autoplay True to enable autoplay, false otherwise.
     */
    public function setAutoplay(bool $autoplay)
    {
        $this->autoplay = $autoplay;
    }

    /**
     * Override the player's playAll setting for playlist auto-advance for this URL.
     *
     * @see https://docs.theplatform.com/help/player-player-playall
     *
     * @param bool $playAll
     */
    public function setPlayAll(bool $playAll)
    {
        $this->playAll = $playAll;
    }
}
