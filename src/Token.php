<?php

namespace Lullabot\Mpx;

/**
 * Class representing an MPX authentication token.
 *
 * @see https://docs.theplatform.com/help/wsf-signin-method
 */
class Token
{
    /**
     * The value of the token, as returned by the signIn() method.
     *
     * @var string
     */
    protected $value;

    /**
     * @var int
     */
    protected $lifetime;

    /**
     * @todo This is not documented in thePlatform.
     *
     * @var int
     */
    protected $expiration;

    /**
     * Construct a new authentication token for a user.
     *
     * @param string $value
     *                         The value of the authentication token as returned by MPX.
     * @param int    $lifetime
     */
    public function __construct($value, $lifetime)
    {
        //assert('$lifetime > 0 /* $lifetime should be greater than zero */');
        $this->value = $value;
        $this->lifetime = $lifetime;
        $this->expiration = time() + $lifetime;
    }

    /**
     * Create a token from an MPX signInResponse.
     *
     * @todo Use something !array for the type.
     *
     * @param array $data
     *                    The MPX response data.
     *
     * @return \Lullabot\Mpx\Token
     *                    A new MPX token.
     */
    public static function fromResponse(array $data): self
    {
        // @todo fix this as idle != duration.
        // @todo We need to store the date this token was created.
        $lifetime = (int) floor(min($data['signInResponse']['duration'], $data['signInResponse']['idleTimeout']) / 1000);
        $token = new self($data['signInResponse']['token'], $lifetime);

        return $token;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getLifetime()
    {
        return $this->lifetime;
    }

    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * @param int $duration
     *
     * @return bool
     */
    public function isValid($duration = 0)
    {
        return $this->getExpiration() > time() + $duration;
    }

    /**
     * Return the value of this token.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }
}
