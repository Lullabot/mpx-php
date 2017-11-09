<?php

namespace Mpx\Tests\Unit;

use Mpx\Token;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Mpx\Token
 */
class TokenTest extends TestCase {

    /**
     * @covers ::__construct
     * @covers ::__toString
     * @covers ::getValue
     * @covers ::getLifetime
     * @covers ::getExpiration
     */
    public function testToken() {
        $time = time();
        $token = new Token('value', 30);
        $this->assertSame('value', (string) $token);
        $this->assertSame('value', $token->getValue());
        $this->assertSame(30, $token->getLifetime());
        $this->assertSame($time + 30, $token->getExpiration());
    }

    /**
     * @covers ::__construct
     * @covers ::isValid
     */
    public function testTokenExpiration() {
        $token = new Token('value', -5);
        $this->assertFalse($token->isValid());
        $this->assertFalse($token->isValid(30));
        $this->assertFalse($token->isValid(60));

        // A token with the same expiration as the current time should not be valid.
        $token = new Token('value', 0);
        $this->assertFalse($token->isValid());
        $this->assertFalse($token->isValid(30));
        $this->assertFalse($token->isValid(60));

        // Test that a token expires after passing time.
        $token = new Token('value', 1);
        $this->assertTrue($token->isValid());
        sleep(2);
        $this->assertFalse($token->isValid());

        $token = new Token('value', 60);
        $this->assertTrue($token->isValid());
        $this->assertTrue($token->isValid(30));
        $this->assertFalse($token->isValid(60));
    }

}
