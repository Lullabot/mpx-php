<?php

namespace Lullabot\Mpx\Service\Player;

use function GuzzleHttp\Psr7\build_query;
use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\ObjectInterface;
use Lullabot\Mpx\DataService\PublicIdentifierInterface;
use Lullabot\Mpx\ToUriInterface;
use Psr\Http\Message\UriInterface;

/**
 * Represents a player URL, suitable for embedding with an iframe.
 *
 * By default, URLs are generated using the media public ID.
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
     * @var PublicIdentifierInterface
     */
    private $player;

    /**
     * The media that is being played.
     *
     * @var PublicIdentifierInterface
     */
    private $media;

    /**
     * Should autoPlay be overridden?
     *
     * @var bool
     */
    private $autoPlay;

    /**
     * Should the playAll setting be overridden?
     *
     * @var bool
     */
    private $playAll;

    /**
     * Should this player URL be rendered so it can be embedded?
     *
     * @see https://docs.theplatform.com/help/displaying-mpx-players-to-your-audience#tp-toc17
     *
     * @var bool
     */
    private $embed;

    /**
     * Should this player URL be rendered using media GUIDs instead of public IDs?
     *
     * @var bool
     */
    private $byGuid;

    /**
     * Url constructor.
     *
     * @param PublicIdentifierInterface $account The account the player is owned by.
     * @param PublicIdentifierInterface $player  The player to play $media with.
     * @param ObjectInterface           $media   The media to play.
     */
    public function __construct(PublicIdentifierInterface $account, PublicIdentifierInterface $player, ObjectInterface $media
    ) {
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
        $str = $this::BASE_URL.$this->account->getPid().'/'.$this->player->getPid();

        if ($this->embed) {
            $str .= '/embed';
        }

        if ($this->byGuid) {
            $ownerId = $this->media->getOwnerId();
            $parts = explode('/', $ownerId);
            $numeric_id = end($parts);

            $uri = new Uri($str.'/select/media/guid/'.$numeric_id.'/'.$this->media->getGuid());
        } else {
            $uri = new Uri($str.'/select/media/'.$this->media->getPid());
        }

        $query_parts = [];

        $query_parts['autoPlay'] = $this->autoPlay ? 'true' : 'false';
        $query_parts['playAll'] = $this->playAll ? 'true' : 'false';

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
     * @param bool $autoPlay True to enable autoPlay, false otherwise.
     *
     * @return Url
     */
    public function withAutoplay(bool $autoPlay): self
    {
        if ($this->autoPlay == $autoPlay) {
            return $this;
        }

        $url = clone $this;
        $url->autoPlay = $autoPlay;

        return $url;
    }

    /**
     * Override the player's playAll setting for playlist auto-advance for this URL.
     *
     * @see https://docs.theplatform.com/help/player-player-playall
     *
     * @param bool $playAll
     *
     * @return Url
     */
    public function withPlayAll(bool $playAll): self
    {
        if ($this->playAll == $playAll) {
            return $this;
        }

        $url = clone $this;
        $url->playAll = $playAll;

        return $url;
    }

    /**
     * @param bool $embed
     *
     * @return Url
     */
    public function withEmbed(bool $embed): self
    {
        if ($this->embed == $embed) {
            return $this;
        }

        $url = clone $this;
        $url->embed = $embed;

        return $url;
    }

    /**
     * Return a Url using media reference GUIDs instead of media public IDs.
     *
     * @return Url
     */
    public function withMediaByGuid(): self
    {
        if ($this->byGuid) {
            return $this;
        }

        $url = clone $this;
        $url->byGuid = true;

        return $url;
    }

    /**
     * Return a Url using media public IDs instead of media reference Ids.
     *
     * @return Url
     */
    public function withMediaByPublicId(): self
    {
        if (!$this->byGuid) {
            return $this;
        }

        $url = clone $this;
        $url->byGuid = false;

        return $url;
    }
}
