<?php

namespace Lullabot\Mpx\Service\IdentityManagement;

use Lullabot\Mpx\Cache\Adapter\PHPArray\ArrayCachePool;
use Lullabot\Mpx\Client;
use Lullabot\Mpx\Exception\TokenNotFoundException;
use Lullabot\Mpx\Token;
use Lullabot\Mpx\TokenCachePool;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\PersistingStoreInterface;

/**
 * Defines a class for authenticating a user with mpx.
 *
 * @see http://help.theplatform.com/display/wsf2/Identity+management+service+API+reference
 * @see http://help.theplatform.com/display/wsf2/User+operations
 */
class UserSession
{
    use LoggerAwareTrait;

    /**
     * The URL to sign in a user.
     *
     * @deprecated Use \Lullabot\Mpx\Service\IdentityManagement\SignInFlow::SIGN_IN_URL instead.
     */
    final public const SIGN_IN_URL = SignInFlow::SIGN_IN_URL;

    /**
     * The URL to sign out a given token for a user.
     *
     * @deprecated Use \Lullabot\Mpx\Service\IdentityManagement\SignInFlow::SIGN_OUT_URL instead.
     */
    final public const SIGN_OUT_URL = SignInFlow::SIGN_OUT_URL;

    /**
     * @var \Lullabot\Mpx\Client
     */
    protected $client;

    /**
     * The backend lock store used to store a lock when signing in to mpx.
     *
     * @var \Symfony\Component\Lock\PersistingStoreInterface
     */
    protected $store;

    /**
     * The cache of authentication tokens.
     *
     * @var \Lullabot\Mpx\TokenCachePool
     */
    protected $tokenCachePool;

    /**
     * The user to authenticate as.
     *
     * @var \Lullabot\Mpx\Service\IdentityManagement\UserInterface
     */
    protected $user;

    /**
     * The flow used to acquire and send authentication tokens.
     *
     * @var \Lullabot\Mpx\Service\IdentityManagement\AuthenticationFlowInterface
     */
    protected $flow;

    /**
     * Construct a new mpx user.
     *
     * @param UserInterface                    $user           The user to authenticate as.
     * @param Client                           $client         The client used to access mpx.
     * @param PersistingStoreInterface|null    $store          (optional) The lock backend to store locks in.
     * @param TokenCachePool|null              $tokenCachePool (optional) The cache of authentication tokens.
     * @param AuthenticationFlowInterface|null $flow           (optional) The authentication flow to use. Defaults to
     *                                                         signing in against the identity management service.
     *
     * @see NullLogger To disable logging of token requests.
     */
    public function __construct(UserInterface $user, Client $client, ?PersistingStoreInterface $store = null, ?TokenCachePool $tokenCachePool = null, ?AuthenticationFlowInterface $flow = null)
    {
        $this->user = $user;
        $this->client = $client;
        $this->store = $store;
        $this->flow = $flow ?? new SignInFlow();
        if (!$tokenCachePool) {
            $tokenCachePool = new TokenCachePool(new ArrayCachePool());
        }
        $this->tokenCachePool = $tokenCachePool;
        $this->logger = new NullLogger();
    }

    /**
     * Get a current authentication token for the account.
     *
     * This method will automatically generate a new token if one does not exist.
     *
     * @todo Do we want to make this async?
     *
     * @param int  $duration (optional) The number of seconds for which the token should be valid.
     * @param bool $reset    Force fetching a new token, even if one exists.
     *
     * @return Token A valid mpx authentication token.
     */
    public function acquireToken(?int $duration = null, bool $reset = false): Token
    {
        if ($reset) {
            $this->tokenCachePool->deleteToken($this);
        }

        // We assume that the cache is backed by shared storage across multiple
        // requests. In that case, it's possible for another thread to set a
        // token between the above delete and the next try block.
        try {
            $token = $this->tokenCachePool->getToken($this);
        } catch (TokenNotFoundException) {
            $token = $this->signInWithLock($duration);
        }

        return $token;
    }

    /**
     * Sign in the user and return the current token.
     *
     * @param int $duration (optional) The number of seconds for which the token should be valid.
     */
    protected function signIn($duration = null): Token
    {
        $token = $this->flow->acquire($this->user, $this->client, $duration);

        // Save the token to the cache before returning it.
        $this->tokenCachePool->setToken($this, $token);

        $this->logger->info(
            'Retrieved a new mpx token {token} for user {username} that expires on {date}.',
            [
                'token' => $token->getValue(),
                'username' => $this->flow->identifier($this->user),
                'date' => date(\DATE_ISO8601, $token->getExpiration()),
            ]
        );

        return $token;
    }

    /**
     * Sign out the user.
     */
    public function signOut()
    {
        $this->flow->revoke($this->tokenCachePool->getToken($this), $this->client);

        $this->tokenCachePool->deleteToken($this);
    }

    /**
     * Sign in to mpx, with a lock to prevent sign-in stampedes.
     *
     * @param int|null $duration (optional) The number of seconds that the sign-in token should be valid for.
     *
     * @return Token The token.
     */
    protected function signInWithLock(?int $duration = null): Token
    {
        if ($this->store) {
            $factory = new LockFactory($this->store);
            $factory->setLogger($this->logger);
            $lock = $factory->createLock($this->flow->identifier($this->user), 10);

            // Blocking means this will throw an exception on failure.
            $lock->acquire(true);
        }

        try {
            // It's possible another thread has signed in for us, so check for a token first.
            $token = $this->tokenCachePool->getToken($this);
        } catch (TokenNotFoundException) {
            // We have the lock, and there's no token, so sign in.
            $token = $this->signIn($duration);
        }

        return $token;
    }

    /**
     * Return the user associated with this session.
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * Return the authentication flow associated with this session.
     */
    public function getFlow(): AuthenticationFlowInterface
    {
        return $this->flow;
    }
}
