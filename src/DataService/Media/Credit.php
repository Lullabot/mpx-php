<?php

namespace Lullabot\Mpx\DataService\Media;

class Credit
{
    /**
     * The role that is being credited.
     *
     * @var string
     */
    protected $role;

    /**
     * The role scheme for the credit.
     *
     * @var string
     */
    protected $scheme;

    /**
     * The person or entity that is being credited.
     *
     * @var string
     */
    protected $value;

    /**
     * Returns the role that is being credited.
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * Set the role that is being credited.
     */
    public function setRole(?string $role)
    {
        $this->role = $role;
    }

    /**
     * Returns the role scheme for the credit.
     */
    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    /**
     * Set the role scheme for the credit.
     */
    public function setScheme(?string $scheme)
    {
        $this->scheme = $scheme;
    }

    /**
     * Returns the person or entity that is being credited.
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Set the person or entity that is being credited.
     */
    public function setValue(?string $value)
    {
        $this->value = $value;
    }
}
