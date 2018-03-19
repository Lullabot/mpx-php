<?php

namespace Lullabot\Mpx\Tests\Unit\Service\AccessManagement;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Lullabot\Mpx\Service\AccessManagement\ResolveAllUrls;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\Tests\JsonResponse;
use Lullabot\Mpx\Tests\MockClientTrait;
use Lullabot\Mpx\TokenCachePool;
use Lullabot\Mpx\User;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * @coversDefaultClass \Lullabot\Mpx\Service\AccessManagement\ResolveAllUrls
 */
class ResolveAllUrlsTest extends TestCase {
    use MockClientTrait;

    /**
     * @covers ::load
     * @covers ::__construct
     * @covers ::getService
     * @covers ::getResolved
     */
    public function testLoad() {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'resolveAllUrls.json'),
        ]);
        $user = new User('USER-NAME', 'correct-password');
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());
        $session = new UserSession($client, $user, $tokenCachePool, new NullLogger());
        /** @var \Lullabot\Mpx\Service\AccessManagement\ResolveAllUrls $r */
        $r = ResolveAllUrls::load($session, 'Media Data Service')->wait();
        $this->assertEquals('Media Data Service', $r->getService());
        $this->assertEquals(["http://data.media.theplatform.com/media"], $r->getResolved());
    }
}
