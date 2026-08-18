<?php

namespace Lullabot\Mpx\Tests\Unit\Service\IdentityManagement;

use GuzzleHttp\Psr7\Query;
use Lullabot\Mpx\DataService\Access\Account;
use Lullabot\Mpx\Service\IdentityManagement\SignInFlow;
use Lullabot\Mpx\Service\IdentityManagement\User;
use Lullabot\Mpx\Tests\JsonResponse;
use Lullabot\Mpx\Tests\MockClientTrait;
use Lullabot\Mpx\Token;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @coversDefaultClass \Lullabot\Mpx\Service\IdentityManagement\SignInFlow
 */
class SignInFlowTest extends TestCase
{
    use MockClientTrait;

    /**
     * @covers ::acquire
     */
    public function testAcquire(): void
    {
        $client = $this->getMockClient([
            function (RequestInterface $request) {
                $this->assertStringStartsWith(SignInFlow::SIGN_IN_URL, (string) $request->getUri()->withQuery(''));
                $parts = Query::parse($request->getUri()->getQuery());
                $this->assertEquals('1.0', $parts['schema']);
                $this->assertEquals('json', $parts['form']);
                $this->assertArrayNotHasKey('_duration', $parts);
                $this->assertEquals('Basic '.base64_encode('mpx/USER-NAME:correct-password'), $request->getHeaderLine('Authorization'));

                return new JsonResponse(200, [], 'signin-success.json');
            },
        ]);

        $token = (new SignInFlow())->acquire(new User('mpx/USER-NAME', 'correct-password'), $client);
        $this->assertEquals('TOKEN-VALUE', $token->getValue());
    }

    /**
     * Test that a requested duration is sent in milliseconds.
     *
     * @covers ::acquire
     */
    public function testAcquireWithDuration(): void
    {
        $client = $this->getMockClient([
            function (RequestInterface $request) {
                $parts = Query::parse($request->getUri()->getQuery());
                $this->assertEquals(300000, $parts['_duration']);
                $this->assertEquals(300000, $parts['_idleTimeout']);

                return new JsonResponse(200, [], 'signin-success.json');
            },
        ]);

        (new SignInFlow())->acquire(new User('mpx/USER-NAME', 'correct-password'), $client, 300);
    }

    /**
     * @covers ::apply
     */
    public function testApplySetsQueryParameter(): void
    {
        $token = new Token('http://example.com/User/1', 'TOKEN-VALUE', 3600);
        $options = (new SignInFlow())->apply($token, ['query' => ['schema' => '1.0']]);

        $this->assertEquals(['schema' => '1.0', 'token' => 'TOKEN-VALUE'], $options['query']);
        $this->assertArrayNotHasKey('headers', $options);
    }

    /**
     * Test that the account context is not sent by this flow.
     *
     * @covers ::apply
     */
    public function testApplyIgnoresAccount(): void
    {
        $account = new Account();
        $account->setMpxId($this->accountUri());
        $token = new Token('http://example.com/User/1', 'TOKEN-VALUE', 3600);

        $this->assertEquals(
            (new SignInFlow())->apply($token, []),
            (new SignInFlow())->apply($token, [], $account)
        );
    }

    /**
     * @covers ::identifier
     */
    public function testIdentifier(): void
    {
        $this->assertEquals('mpx/USER-NAME', (new SignInFlow())->identifier(new User('mpx/USER-NAME', 'password')));
    }

    /**
     * @covers ::revoke
     */
    public function testRevoke(): void
    {
        $client = $this->getMockClient([
            function (RequestInterface $request) {
                $this->assertStringStartsWith(SignInFlow::SIGN_OUT_URL, (string) $request->getUri()->withQuery(''));
                $parts = Query::parse($request->getUri()->getQuery());
                $this->assertEquals('TOKEN-VALUE', $parts['_token']);

                return new JsonResponse(200, [], 'signout.json');
            },
        ]);

        (new SignInFlow())->revoke(new Token('http://example.com/User/1', 'TOKEN-VALUE', 3600), $client);
    }

    private function accountUri(): UriInterface
    {
        return new \GuzzleHttp\Psr7\Uri('http://access.auth.theplatform.com/data/Account/1');
    }
}
