<?php

namespace Lullabot\Mpx\Tests\Unit;

use Lullabot\Mpx\Token;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Lullabot\Mpx\Token
 */
class TokenTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::__toString
     * @covers ::getValue
     * @covers ::getLifetime
     * @covers ::getExpiration
     */
    public function testToken()
    {
        $time = time();
        $token = new Token('https://example.com/idm/data/User/mpx/123456', 'value', 30);
        $this->assertSame('value', (string) $token);
        $this->assertSame('value', $token->getValue());
        $this->assertSame(30, $token->getLifetime());
        $this->assertSame($time + 30, $token->getExpiration());
    }

    /**
     * @covers ::__construct
     * @covers ::isValid
     */
    public function testTokenExpiration()
    {
        // We use a serialized token as a fixture as we don't allow constructing
        // of expired tokens.
        $token = include __DIR__.'/../../fixtures/ExpiredToken.php';
        $this->assertFalse($token->isValid());
        $this->assertFalse($token->isValid(30));
        $this->assertFalse($token->isValid(60));

        // Test that a token expires after passing time.
        $token = new Token('https://example.com/idm/data/User/mpx/123456', 'value', 1);
        $this->assertTrue($token->isValid());
        sleep(1);
        $this->assertFalse($token->isValid());

        $token = new Token('https://identity.auth.theplatform.com/idm/data/User/mpx/2685072', 'value', 59);
        $this->assertTrue($token->isValid());
        $this->assertTrue($token->isValid(30));
        $this->assertFalse($token->isValid(60));
    }

    /**
     * Test an expiration that will never pass.
     *
     * @covers ::__construct
     */
    public function testInvalidExpiration()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$lifetime must be greater than zero.');
        new Token('https://identity.auth.theplatform.com/idm/data/User/mpx/2685072', 'value', 0);
    }

    /**
     * Test getting the full User ID.
     *
     * @covers ::__construct
     * @covers ::getUserId
     */
    public function testGetUserId()
    {
        $userId = 'https://identity.auth.theplatform.com/idm/data/User/mpx/2685072';
        $token = new Token($userId, 'value', 59);
        $this->assertEquals($userId, $token->getUserId());
    }

    /**
     * Test that created is a timestamp.
     *
     * @covers ::getCreated
     */
    public function testCreated()
    {
        $token = new Token('https://identity.auth.theplatform.com/idm/data/User/mpx/2685072', 'value', 59);
        $this->assertInternalType('integer', $token->getCreated());
    }

    /**
     * Test creating a token from an MPX response.
     *
     * @covers ::fromResponseData
     * @covers ::validateData
     */
    public function testFromResponseData()
    {
        $data = json_decode(file_get_contents(__DIR__.'/../../fixtures/signin-success.json'), true);
        $token = Token::fromResponseData($data);
        $this->assertSame('TOKEN-VALUE', (string) $token);
        $this->assertSame('TOKEN-VALUE', $token->getValue());
        $this->assertSame(14400, $token->getLifetime());
    }

    /**
     * Test that a missing signInResponse throws an exception.
     *
     * @covers ::fromResponseData
     * @covers ::validateData
     */
    public function testValidateDataNoRoot()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('signInResponse key is missing.');
        Token::fromResponseData([]);
    }

    /**
     * Test missing required keys within a signInResponse.
     *
     * @dataProvider validateDataProvider
     * @covers ::validateData
     */
    public function testInvalidResponseData($data, $key)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Required key %s is missing.', $key));
        Token::fromResponseData(['signInResponse' => $data]);
    }

    /**
     * Data provider for testing error validation.
     *
     * @return array An array with an invalid error and the missing key.
     */
    public function validateDataProvider()
    {
        $required = [
            'duration' => 123,
            'idleTimeout' => 456,
            'token' => 'token-value',
        ];
        $data = array_map(function ($value) use (&$required) {
            end($required);
            $key = key($required);
            array_pop($required);

            return [$required, $key];
        }, $required);

        return $data;
    }
}
