<?php

namespace Lullabot\Mpx\Tests\Unit\Service\IdentityManagement;

use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\Access\Account;
use Lullabot\Mpx\Service\IdentityManagement\ServiceTokenFlow;
use Lullabot\Mpx\Service\IdentityManagement\ServiceUser;
use Lullabot\Mpx\Service\IdentityManagement\User;
use Lullabot\Mpx\Tests\JsonResponse;
use Lullabot\Mpx\Tests\MockClientTrait;
use Lullabot\Mpx\Token;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * @coversDefaultClass \Lullabot\Mpx\Service\IdentityManagement\ServiceTokenFlow
 */
class ServiceTokenFlowTest extends TestCase
{
    use MockClientTrait;

    /**
     * Test that credentials are sent as HTTP Basic auth, with no other data.
     *
     * @covers ::acquire
     * @covers ::clientId
     * @covers ::clientSecret
     */
    public function testAcquire(): void
    {
        $client = $this->getMockClient([
            function (RequestInterface $request) {
                $this->assertStringStartsWith(ServiceTokenFlow::SERVICE_TOKEN_URL, (string) $request->getUri()->withQuery(''));
                $this->assertEquals('Basic '.base64_encode('CLIENT-ID:CLIENT-SECRET'), $request->getHeaderLine('Authorization'));
                $this->assertEmpty((string) $request->getBody());

                // The endpoint takes no parameters of its own.
                $parts = Query::parse($request->getUri()->getQuery());
                $this->assertArrayNotHasKey('schema', $parts);
                $this->assertArrayNotHasKey('form', $parts);
                $this->assertArrayNotHasKey('_duration', $parts);
                $this->assertArrayNotHasKey('_idleTimeout', $parts);

                return new JsonResponse(200, [], 'service-token-success.json');
            },
        ]);

        $token = (new ServiceTokenFlow())->acquire(new ServiceUser('CLIENT-ID', 'CLIENT-SECRET'), $client);
        $this->assertEquals('SERVICE-TOKEN-VALUE', $token->getValue());
        $this->assertEquals(7200, $token->getLifetime());
        $this->assertEquals('https://identity.auth.theplatform.com/idm/data/User/service-sso/f3dc415c-efcd-4e1d-8e6c-766e3a1a0666', $token->getUserId());
    }

    /**
     * Test that a requested duration is ignored, as the lifetime is set server-side.
     *
     * @covers ::acquire
     */
    public function testAcquireIgnoresDuration(): void
    {
        $client = $this->getMockClient([
            function (RequestInterface $request) {
                $parts = Query::parse($request->getUri()->getQuery());
                $this->assertArrayNotHasKey('_duration', $parts);

                return new JsonResponse(200, [], 'service-token-success.json');
            },
        ]);

        $token = (new ServiceTokenFlow())->acquire(new ServiceUser('CLIENT-ID', 'CLIENT-SECRET'), $client, 300);
        $this->assertEquals(7200, $token->getLifetime());
    }

    /**
     * Test that a plain user falls back to its username and password.
     *
     * @covers ::clientId
     * @covers ::clientSecret
     */
    public function testAcquireWithPlainUser(): void
    {
        $client = $this->getMockClient([
            function (RequestInterface $request) {
                $this->assertEquals('Basic '.base64_encode('mpx/USER-NAME:correct-password'), $request->getHeaderLine('Authorization'));

                return new JsonResponse(200, [], 'service-token-success.json');
            },
        ]);

        (new ServiceTokenFlow())->acquire(new User('mpx/USER-NAME', 'correct-password'), $client);
    }

    /**
     * Test that the account context is sent as the Basic auth username.
     *
     * @covers ::apply
     */
    public function testApplyWithAccount(): void
    {
        $account = new Account();
        $account->setMpxId(new Uri('http://access.auth.theplatform.com/data/Account/1'));
        $token = new Token('http://example.com/User/1', 'SERVICE-TOKEN-VALUE', 7200);

        $options = (new ServiceTokenFlow())->apply($token, [], $account);

        $this->assertEquals(
            'Basic '.base64_encode('http://access.auth.theplatform.com/data/Account/1:SERVICE-TOKEN-VALUE'),
            $options['headers']['Authorization']
        );
        // The token must never be exposed in a URL.
        $this->assertArrayNotHasKey('query', $options);
    }

    /**
     * Test that an absent account context sends an empty Basic auth username.
     *
     * @covers ::apply
     */
    public function testApplyWithoutAccount(): void
    {
        $token = new Token('http://example.com/User/1', 'SERVICE-TOKEN-VALUE', 7200);
        $options = (new ServiceTokenFlow())->apply($token, [], null);

        $this->assertEquals('Basic '.base64_encode(':SERVICE-TOKEN-VALUE'), $options['headers']['Authorization']);
    }

    /**
     * Test that existing request options are preserved.
     *
     * @covers ::apply
     */
    public function testApplyPreservesOptions(): void
    {
        $token = new Token('http://example.com/User/1', 'SERVICE-TOKEN-VALUE', 7200);
        $options = (new ServiceTokenFlow())->apply($token, [
            'query' => ['schema' => '1.0'],
            'headers' => ['Accept' => 'application/json'],
        ]);

        $this->assertEquals(['schema' => '1.0'], $options['query']);
        $this->assertEquals('application/json', $options['headers']['Accept']);
    }

    /**
     * Test that this flow never shares a cache key with the sign-in flow.
     *
     * @covers ::identifier
     */
    public function testIdentifierIsNamespaced(): void
    {
        $user = new ServiceUser('CLIENT-ID', 'CLIENT-SECRET');
        $identifier = (new ServiceTokenFlow())->identifier($user);

        $this->assertEquals('service-token:CLIENT-ID', $identifier);
        $this->assertNotEquals($user->getMpxUsername(), $identifier);
    }

    /**
     * @covers ::revoke
     */
    public function testRevokeIssuesNoRequest(): void
    {
        // An empty queue means the mock handler throws if a request is made.
        $client = $this->getMockClient([]);
        (new ServiceTokenFlow())->revoke(new Token('http://example.com/User/1', 'SERVICE-TOKEN-VALUE', 7200), $client);
        $this->addToAssertionCount(1);
    }
}
