<?php

namespace Lullabot\Mpx\DataService;

use Psr\Http\Message\UriInterface;

/**
 * Trait for objects implementing advertising policies.
 */
trait AdPolicyDataTrait
{
    /**
     * The identifier for the advertising policy for this object.
     *
     * @var UriInterface
     */
    protected $adPolicyId;

    /**
     * Returns the id of the AdPolicy associated with this content.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getAdPolicyId(): UriInterface
    {
        return $this->adPolicyId;
    }

    /**
     * Set the id of the AdPolicy associated with this content.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function setAdPolicyId($adPolicyId)
    {
        $this->adPolicyId = $adPolicyId;
    }
}
