<?php

namespace Lullabot\Mpx;

/**
 * Class representing an MPX authentication token.
 *
 * @see https://docs.theplatform.com/help/wsf-signin-method
 */
class Token implements \Stringable
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
     * The full ID of the user, as a URL.
     *
     * While this properly belongs on a User, MPX returns it in the token as
     * well, and many API calls need the account to be specified.
     *
     * @var string
     */
    protected $userId;

    /**
     * The time this token was created. This is determined client-side.
     *
     * @var int
     */
    protected $created;

    /**
     * Construct a new authentication token for a user.
     *
     * @param string $userId   The full user ID as a URL.
     * @param string $value    The value of the authentication token as returned by MPX.
     * @param int    $lifetime The number of seconds the token is valid for.
     */
    public function __construct(string $userId, string $value, int $lifetime)
    {
        if ($lifetime <= 0) {
            throw new \InvalidArgumentException('$lifetime must be greater than zero.');
        }

        $this->userId = $userId;
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
    public static function fromResponseData(array $data): self
    {
        // @todo fix this as idle != duration.
        // @todo We need to store the date this token was created.
        static::validateData($data);
        $lifetime = (int) floor(min($data['signInResponse']['duration'], $data['signInResponse']['idleTimeout']) / 1000);
        $token = new self($data['signInResponse']['userId'], $data['signInResponse']['token'], $lifetime);

        return $token;
    }

    /**
     * Validate all required keys in a sign-in response.
     *
     * @param array $data The response data from MPX.
     *
     * @throws \InvalidArgumentException Thrown when $data is missing required data.
     */
    private static function validateData(array $data)
    {
        if (!isset($data['signInResponse'])) {
            throw new \InvalidArgumentException('signInResponse key is missing.');
        }

        $required = [
            'duration',
            'idleTimeout',
            'token',
        ];
        foreach ($required as $key) {
            if (empty($data['signInResponse'][$key])) {
                throw new \InvalidArgumentException(sprintf('Required key %s is missing.', $key));
            }
        }
    }

    /**
     * Get the value of this token.
     *
     * @return string The unique token string.
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
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Return the time this token was created.
     *
     * @return int The Unix timestamp of when this token was created.
     */
    public function getCreated(): int
    {
        return $this->created;
    }

    /**
     * Return the user ID associated with this token.
     */
    public function getUserId(): string
    {
        return $this->userId;
    }
}
