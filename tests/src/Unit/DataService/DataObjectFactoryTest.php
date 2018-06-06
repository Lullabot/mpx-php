<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use Cache\Adapter\PHPArray\ArrayCachePool;
use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\AuthenticatedClient;
use Lullabot\Mpx\DataService\Access\Account;
use Lullabot\Mpx\DataService\ObjectListQuery;
use Lullabot\Mpx\DataService\DataObjectFactory;
use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\DataService\Media\Media;
use Lullabot\Mpx\DataService\ObjectList;
use Lullabot\Mpx\DataService\ObjectListIterator;
use Lullabot\Mpx\Service\IdentityManagement\User;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\Tests\JsonResponse;
use Lullabot\Mpx\Tests\MockClientTrait;
use Lullabot\Mpx\TokenCachePool;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\StoreInterface;

/**
 * Tests the DataObjectFactory.
 *
 * @coversDefaultClass \Lullabot\Mpx\DataService\DataObjectFactory
 */
class DataObjectFactoryTest extends TestCase
{
    use MockClientTrait;

    /**
     * Tests loading a URI to an mpx object.
     *
     * @covers ::__construct
     * @covers ::load
     * @covers ::getObjectSerializer
     * @covers ::deserialize
     */
    public function testLoad()
    {
        $manager = DataServiceManager::basicDiscovery();
        $service = $manager->getDataService('Media Data Service', 'Media', '1.10');
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            function (\Psr\Http\Message\RequestInterface $request) {
                $this->assertEquals('https', $request->getUri()->getScheme());
                $this->assertEquals('/media/data/Media/2602559', $request->getUri()->getPath());

                return new JsonResponse(200, [], 'media-object.json');
            },
        ]);
        $user = new User('mpx/username', 'password');
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());
        /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)->getMock();
        $session = new UserSession($user, $client, $store, $tokenCachePool);
        $authenticatedClient = new AuthenticatedClient($client, $session);
        $factory = new DataObjectFactory($service, $authenticatedClient);

        $account = new Account();
        $account->setId(new Uri('http://example.com/1'));
        $id = new Uri('http://data.media.theplatform.com/media/data/Media/2602559');
        $media = $factory->load($id)->wait();
        $this->assertInstanceOf(Media::class, $media);
        $this->assertEquals($id, $media->getId());
    }

    /**
     * Tests the correct path when loading by ID.
     *
     * @covers ::loadByNumericId
     * @covers ::getBaseUri
     */
    public function testLoadByNumericId()
    {
        $manager = DataServiceManager::basicDiscovery();
        $service = $manager->getDataService('Media Data Service', 'Media', '1.10');
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'resolveDomain.json'),
            function (\Psr\Http\Message\RequestInterface $request) {
                $this->assertEquals('https', $request->getUri()->getScheme());
                $this->assertEquals('/media/data/Media/12345', $request->getUri()->getPath());

                return new JsonResponse(200, [], 'media-object.json');
            },
        ]);
        $user = new User('mpx/username', 'password');
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());
        /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)->getMock();
        $session = new UserSession($user, $client, $store, $tokenCachePool);
        $authenticatedClient = new AuthenticatedClient($client, $session);
        $factory = new DataObjectFactory($service, $authenticatedClient);

        $account = new Account();
        $account->setId(new Uri('http://example.com/1'));
        $media = $factory->loadByNumericId(12345, $account)->wait();
        $this->assertInstanceOf(Media::class, $media);
    }

    /**
     * Tests fetching a list of objects.
     *
     * @covers ::selectRequest
     * @covers ::getBaseUri
     * @covers ::deserializeObjectList
     */
    public function testSelectRequest()
    {
        $manager = DataServiceManager::basicDiscovery();
        $service = $manager->getDataService('Access Data Service', 'Account', '1.0');
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'select-account.json'),
        ]);
        $user = new User('mpx/username', 'password');
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());
        /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)->getMock();
        $session = new UserSession($user, $client, $store, $tokenCachePool);
        $authenticatedClient = new AuthenticatedClient($client, $session);
        $factory = new DataObjectFactory($service, $authenticatedClient);
        /** @var ObjectList $objectList */
        $objectList = $factory->selectRequest(new ObjectListQuery())->wait();
        $this->assertEquals(1, $objectList->getEntryCount());
        $this->assertEquals(1, $objectList->getItemsPerPage());
        $this->assertEquals(1, $objectList->getStartIndex());
        $this->assertEquals(1, $objectList->getTotalResults());
        $account = $objectList[0];
        $this->assertInstanceOf(Account::class, $account);
        $this->assertEquals('http://access.auth.theplatform.com/data/Account/55555', $account->getId());
    }

    /**
     * Tests setting the namespace on subentries JSON representation.
     *
     * @covers ::selectRequest
     * @covers ::getBaseUri
     * @covers ::deserializeObjectList
     */
    public function testSelectRequestNS()
    {
        $manager = DataServiceManager::basicDiscovery();
        $service = $manager->getDataService('Media Data Service', 'Media', '1.10');
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'resolveAllUrls.json'),
            new JsonResponse(200, [], 'select-media.json'),
        ]);
        $user = new User('mpx/username', 'password');
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());
        /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)->getMock();
        $session = new UserSession($user, $client, $store, $tokenCachePool);
        $authenticatedClient = new AuthenticatedClient($client, $session);
        $factory = new DataObjectFactory($service, $authenticatedClient);
        /** @var ObjectList $objectList */
        $objectList = $factory->selectRequest(new ObjectListQuery())->wait();
        $this->assertEquals(['prefix1' => 'http://www.example.com/xml'], $objectList[0]->getJson()['$xmlns']);
    }

    /**
     * @covers ::select
     */
    public function testSelect()
    {
        $manager = DataServiceManager::basicDiscovery();
        $service = $manager->getDataService('Access Data Service', 'Account', '1.0');
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'select-account.json'),
        ]);
        $user = new User('mpx/username', 'password');
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());
        /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)->getMock();
        $session = new UserSession($user, $client, $store, $tokenCachePool);
        $authenticatedClient = new AuthenticatedClient($client, $session);
        $factory = new DataObjectFactory($service, $authenticatedClient);
        $iterator = $factory->select(new ObjectListQuery());
        $this->assertInstanceOf(ObjectListIterator::class, $iterator);
    }

    /**
     * Tests loading an object without specifying an account.
     *
     * @covers ::getBaseUri
     */
    public function testNullAccount()
    {
        $manager = DataServiceManager::basicDiscovery();
        $service = $manager->getDataService('Media Data Service', 'Media', '1.10');
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'resolveAllUrls.json'),
            new JsonResponse(200, [], 'media-object.json'),
        ]);
        $user = new User('mpx/username', 'password');
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());
        /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)->getMock();
        $session = new UserSession($user, $client, $store, $tokenCachePool);
        $authenticatedClient = new AuthenticatedClient($client, $session);
        $factory = new DataObjectFactory($service, $authenticatedClient);
        /** @var ObjectList $objectList */
        $media = $factory->loadByNumericId(2602559)->wait();
        $this->assertInstanceOf(Media::class, $media);
        $this->assertEquals('http://data.media.theplatform.com/media/data/Media/2602559', $media->getId());
    }
}
