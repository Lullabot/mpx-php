<?php

namespace Mpx;

class Token {

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
     * @var int
     */
    protected $expiration;

    /**
     * Token constructor.
     * @param string $value
     * @param int $lifetime
     */
    public function __construct($value, $lifetime) {
        //assert('$lifetime > 0 /* $lifetime should be greater than zero */');
        $this->value = $value;
        $this->lifetime = $lifetime;
        $this->expiration = time() + $lifetime;
    }

    public function getValue() {
        return $this->value;
    }

    public function getLifetime() {
        return $this->lifetime;
    }

    public function getExpiration() {
        return $this->expiration;
    }

    /**
     * @param int $duration
     *
     * @return bool
     */
    public function isValid($duration = NULL) {
        return $this->getExpiration() > time() + $duration;
    }

    public function __toString() {
        return $this->value;
    }

}
