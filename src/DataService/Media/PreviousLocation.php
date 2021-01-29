<?php

namespace Lullabot\Mpx\DataService\Media;

use GuzzleHttp\Psr7\Uri;

/**
 * Represents a MediaFile that has been moved.
 *
 * @see https://docs.theplatform.com/help/media-previouslocation-object
 */
class PreviousLocation
{
    /**
     * The MediaFile filePath value at the time moveFile was called.
     *
     * @var string
     */
    protected $filePath;

    /**
     * The MediaFile serverId value at the time moveFile was called.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $serverId;

    /**
     * The MediaFile version value at the time moveFile was called.
     *
     * @var int
     */
    protected $version;

    /**
     * Returns the MediaFile filePath value at the time moveFile was called.
     *
     * @return string
     */
    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    /**
     * Set the MediaFile filePath value at the time moveFile was called.
     *
     * @param string $filePath
     */
    public function setFilePath(?string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Returns the MediaFile serverId value at the time moveFile was called.
     */
    public function getServerId(): \Psr\Http\Message\UriInterface
    {
        if (!$this->serverId) {
            return new Uri();
        }

        return $this->serverId;
    }

    /**
     * Set the MediaFile serverId value at the time moveFile was called.
     */
    public function setServerId(\Psr\Http\Message\UriInterface $serverId)
    {
        $this->serverId = $serverId;
    }

    /**
     * Returns the MediaFile version value at the time moveFile was called.
     *
     * @return int
     */
    public function getVersion(): ?int
    {
        return $this->version;
    }

    /**
     * Set the MediaFile version value at the time moveFile was called.
     *
     * @param int $version
     */
    public function setVersion(?int $version)
    {
        $this->version = $version;
    }
}
