<?php

namespace Lullabot\Mpx\DataService\Player;

/**
 * The PlugInInstance object is a reference to a PlugIn object that has been added to a player configuration.
 *
 * @see https://docs.theplatform.com/help/player-player-plugininstance-object
 */
class PlugInInstance
{
    /**
     * Configuration parameters for the plug-in.
     *
     * @var string[]
     */
    protected $params;

    /**
     * Unique identifier for the plug-in.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $plugInId;

    /**
     * Identifier for the region that hosts the component that will load the plug-in.
     *
     * @var string
     */
    protected $regionName;

    /**
     * Get configuration parameters for the plug-in.
     *
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Set configuration parameters for the plug-in.
     *
     * @param string[] $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * Get the unique identifier for the plug-in.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getPlugInId(): \Psr\Http\Message\UriInterface
    {
        return $this->plugInId;
    }

    /**
     * Set the unique identifier for the plug-in.
     *
     * @param \Psr\Http\Message\UriInterface $plugInId
     */
    public function setPlugInId(\Psr\Http\Message\UriInterface $plugInId)
    {
        $this->plugInId = $plugInId;
    }

    /**
     * Get the identifier for the region that hosts the component that will load the plug-in.
     *
     * @return string
     */
    public function getRegionName(): string
    {
        return $this->regionName;
    }

    /**
     * Set the identifier for the region that hosts the component that will load the plug-in.
     *
     * @param string $regionName
     */
    public function setRegionName(string $regionName)
    {
        $this->regionName = $regionName;
    }
}
