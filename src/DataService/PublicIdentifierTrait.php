<?php

namespace Lullabot\Mpx\DataService;

/**
 * Trait for mpx objects implementing a public identifier.
 */
trait PublicIdentifierTrait
{
    /**
     * A public identifier for the account.
     *
     * @var string
     */
    protected $pid;

    /**
     * Returns a public identifier for the account.
     *
     * @return string
     */
    public function getPid(): string
    {
        return $this->pid;
    }

    /**
     * Set a public identifier for the account.
     *
     * @param string
     */
    public function setPid(string $pid)
    {
        if (strlen($pid) > 64) {
            throw new \InvalidArgumentException('Public Identifiers must not be longer than 64 characters.');
        }
        if ('ASCII' != mb_check_encoding($pid)) {
            throw new \InvalidArgumentException('Public Identifiers must be ASCII encoded strings.');
        }
        $this->pid = $pid;
    }
}
