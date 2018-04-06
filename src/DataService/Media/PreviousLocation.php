<?php

namespace Lullabot\Mpx\DataService\Media;

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
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * Set the MediaFile filePath value at the time moveFile was called.
     *
     * @param string
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Returns the MediaFile serverId value at the time moveFile was called.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getServerId(): \Psr\Http\Message\UriInterface
    {
        return $this->serverId;
    }

    /**
     * Set the MediaFile serverId value at the time moveFile was called.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setServerId($serverId)
    {
        $this->serverId = $serverId;
    }

    /**
     * Returns the MediaFile version value at the time moveFile was called.
     *
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * Set the MediaFile version value at the time moveFile was called.
     *
     * @param int
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }
}
