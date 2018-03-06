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
     * The lifetime in seconds of the token from when it was first created.
     *
     * @var int
     */
    protected $lifetime;

    /**
     * The time this token was created. This is determined client-side.
     *
     * @var int
     */
    protected $created;

    /**
     * Construct a new authentication token for a user.
     *
     * @param string $value    The value of the authentication token as returned by MPX.
     * @param int    $lifetime The number of seconds the token is valid for.
     */
    public function __construct($value, $lifetime)
    {
        if ($lifetime <= 0) {
            throw new \InvalidArgumentException('$lifetime must be greater than zero.');
        }

        $this->value = $value;
        $this->lifetime = $lifetime;
        $this->created = time();
    }

    /**
     * Create a token from an MPX signInResponse.
     *
     * @todo Use something !array for the type.
     *
     * @param array $data The MPX response data.
     *
     * @return \Lullabot\Mpx\Token A new MPX token.
     */
    public static function fromResponse(array $data): self
    {
        // @todo fix this as idle != duration.
        // @todo We need to store the date this token was created.
        $lifetime = (int) floor(min($data['signInResponse']['duration'], $data['signInResponse']['idleTimeout']) / 1000);
        $token = new self($data['signInResponse']['token'], $lifetime);

        return $token;
    }

    /**
     * Get the value of this token.
     *
     * @return string
     *   The unique token string.
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * Return the absolute time this token expires at.
     *
     * @return int
     */
    public function getExpiration()
    {
        return $this->created + $this->lifetime;
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

    /**
     * Return the time this token was created.
     *
     * @return int
     */
    public function getCreated(): int
    {
        return $this->created;
    }
}
