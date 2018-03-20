<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Lullabot\Mpx\DataService\Access\Account;
use Lullabot\Mpx\DataService\DataObjectFactory;
use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\DataService\Media\Media;
use Lullabot\Mpx\Service\AccessManagement\ResolveDomain;
use Lullabot\Mpx\Service\AccessManagement\ResolveDomainResponse;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\Tests\JsonResponse;
use Lullabot\Mpx\Tests\MockClientTrait;
use Lullabot\Mpx\TokenCachePool;
use Lullabot\Mpx\User;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Lock\StoreInterface;

/**
 * Test loading a data object based on an annotation.
 *
 * @coversDefaultClass \Lullabot\Mpx\DataService\DataObjectFactory
 */
class DataObjectFactoryTest extends TestCase
{
    use MockClientTrait;

    /**
     * @covers ::__construct
     * @covers ::load
     * @covers ::deserialize
     */
    public function testLoad()
    {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            function (RequestInterface $request) {
                // Assert that we build the request properly.
                $this->assertEquals('/data/Media/1234', $request->getUri()->getPath());
                $this->assertEquals('schema=1.10&form=cjson&token=TOKEN-VALUE', $request->getUri()->getQuery());

                return new JsonResponse(200, [], 'media-object.json');
            },
        ]);

        $user = new User('USER-NAME', 'SECRET');
        /** @var StoreInterface|\PHPUnit\Framework\MockObject\MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());
        $session = new UserSession($client, $user, $store, $tokenCachePool, new NullLogger());

        /** @var ResolveDomain|\PHPUnit\Framework\MockObject\MockObject $resolveDomain */
        $resolveDomain = $this->getMockBuilder(ResolveDomain::class)
            ->disableOriginalConstructor()
            ->getMock();

        // We mock resolveDomain directly instead of mocking a request.
        $resolveDomain->expects($this->once())->method('resolve')
            ->willReturn(
                new ResolveDomainResponse(['resolveDomainResponse' => ['Media Data Service' => 'https://example.com']])
            );

        $dataServiceManager = DataServiceManager::basicDiscovery();
        $mediaFactory = new DataObjectFactory($dataServiceManager->getDataService('Media Data Service', '/data/Media'), $session, $resolveDomain);
        $promise = $mediaFactory->load(1234, $this->getMockBuilder(Account::class)->getMock());

        $this->assertInstanceOf(Media::class, $promise->wait());
    }
}
