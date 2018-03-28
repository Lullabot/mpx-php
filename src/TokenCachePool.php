<?php

namespace Lullabot\Mpx;

use Lullabot\Mpx\Exception\TokenNotFoundException;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Psr\Cache\CacheItemPoolInterface;

/**
 * The cache of user authentication tokens.
 *
 * MPX allows for multiple active sessions for a given user. However, we only
 * support a single authenticated client per user within a given PHP request.
 *
 * @see https://docs.theplatform.com/help/wsf-signin-method
 */
class TokenCachePool
{
    /**
     * The underlying cache backend.
     *
     * @var \Psr\Cache\CacheItemPoolInterface
     */
    protected $cacheItemPool;

    /**
     * Construct a new cache of user authentication tokens.
     *
     * @param \Psr\Cache\CacheItemPoolInterface $cacheItemPool The underlying cache backend.
     */
    public function __construct(CacheItemPoolInterface $cacheItemPool)
    {
        $this->cacheItemPool = $cacheItemPool;
    }

    /**
     * Set an authentication token for a user.
     *
     * @param \Lullabot\Mpx\Service\IdentityManagement\UserSession $user  The user the token is associated with.
     * @param \Lullabot\Mpx\Token                                  $token The authentication token for the user.
     */
    public function setToken(UserSession $user, Token $token)
    {
        $item = $this->cacheItemPool->getItem($this->cacheKey($user));
        $item->set($token);
        // @todo Test that these values are compatible.
        $item->expiresAfter($token->getLifetime());
        $this->cacheItemPool->save($item);
    }

    /**
     * Get the cached token for a user.
     *
     * @param \Lullabot\Mpx\Service\IdentityManagement\UserSession $user The user to look up tokens for.
     *
     * @return \Lullabot\Mpx\Token The cached token.
     */
    public function getToken(UserSession $user): Token
    {
        // @todo Test that the expiresAfter() call works. We don't want to be caught
        // by cron etc.
        $item = $this->cacheItemPool->getItem($this->cacheKey($user));

        if (!$item->isHit()) {
            throw new TokenNotFoundException($user);
        }

        return $item->get();
    }

    /**
     * Delete a cached token for a user.
     *
     * @param \Lullabot\Mpx\Service\IdentityManagement\UserSession $user The user to delete the token for.
     */
    public function deleteToken(UserSession $user)
    {
        $this->cacheItemPool->deleteItem($this->cacheKey($user));
    }

    /**
     * Generate a cache key for a token, limiting key length.
     *
     * @param \Lullabot\Mpx\Service\IdentityManagement\UserSession $user The user to generate the cache key for.
     *
     * @return string The cache key for the user.
     */
    private function cacheKey(UserSession $user): string
    {
        return md5($user->getUsername());
    }
}
