<?php

namespace Lullabot\Mpx\Exception;

use Lullabot\Mpx\UserSession;

/**
 * Exception thrown when a token is not found in the token cache.
 */
class TokenNotFoundException extends \RuntimeException
{
    /**
     * TokenNotFoundException constructor.
     *
     * @param UserSession     $user     The user the token was not found for.
     * @param int             $code     The exception code.
     * @param \Throwable|null $previous The previous exception, if available.
     */
    public function __construct(UserSession $user, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(sprintf('Token not found for %s.', $user->getUsername()), $code, $previous);
    }
}
