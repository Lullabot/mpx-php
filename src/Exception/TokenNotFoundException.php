<?php

namespace Lullabot\Mpx\Exception;

use Lullabot\Mpx\User;

class TokenNotFoundException extends \RuntimeException
{
    public function __construct(User $user, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(sprintf('Token not found for %s.', $user->getUsername()), $code, $previous);
    }
}
