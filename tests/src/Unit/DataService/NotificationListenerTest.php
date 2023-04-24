<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use Cache\Adapter\PHPArray\ArrayCachePool;
use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\AuthenticatedClient;
use Lullabot\Mpx\DataService\Access\Account;
use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\DataService\Media\Media;
use Lullabot\Mpx\DataService\NotificationListener;
use Lullabot\Mpx\Service\IdentityManagement\User;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\Tests\Fixtures\DummyStoreInterface;
use Lullabot\Mpx\Tests\JsonResponse;
use Lullabot\Mpx\Tests\MockClientTrait;
use Lullabot\Mpx\TokenCachePool;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * Tests listening for notifications.
 *
 * @coversDefaultClass  \Lullabot\Mpx\DataService\NotificationListener
 */
class NotificationListenerTest extends TestCase
{
    use MockClientTrait;

    /**
     * Test fetching the last sync ID.
     *
     * @covers ::__construct
     * @covers ::sync
     */
    public function testSync()
    {
        $notification_id = random_int(1, mt_getrandmax());

        $account_id = 'https://www.example.com/12345';
        $account = new Account();
        $account->setId(new Uri($account_id));

        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'resolveAllUrls.json'),
            function (RequestInterface $request) use ($notification_id, $account_id) {
                $params = Query::parse($request->getUri()->getQuery());
                $this->assertArrayHasKey('account', $params);
                $this->assertEquals($account_id, $params['account']);

                return new JsonResponse(200, [], [
                    [
                        'id' => $notification_id,
                    ],
                ]);
            },
        ]);
        $user = new User('mpx/username', 'password');
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());
        /** @var DummyStoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(DummyStoreInterface::class)->getMock();
        $store->expects($this->any())
            ->method('exists')
            ->willReturn(false);
        $session = new UserSession($user, $client, $store, $tokenCachePool);
        $authenticatedClient = new AuthenticatedClient($client, $session, $account);
        $manager = DataServiceManager::basicDiscovery();
        $service = $manager->getDataService('Media Data Service', 'Media', '1.10');
        $listener = new NotificationListener($authenticatedClient, $service, 'unit-tests');
        $last = $listener->sync()->wait();
        $this->assertEquals($notification_id, $last);
    }

    /**
     * Test listening for notifications.
     *
     * @covers ::__construct
     * @covers ::listen
     * @covers ::deserializeResponse
     */
    public function testListen()
    {
        $account_id = 'https://www.example.com/12345';
        $account = new Account();
        $account->setId(new Uri($account_id));

        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'resolveAllUrls.json'),
            function (RequestInterface $request) use ($account_id) {
                $params = Query::parse($request->getUri()->getQuery());
                $this->assertArrayHasKey('account', $params);
                $this->assertEquals($account_id, $params['account']);

                return new JsonResponse(200, [], 'notification.json');
            },
        ]);
        $user = new User('mpx/username', 'password');
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());
        /** @var DummyStoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(DummyStoreInterface::class)->getMock();
        $store->expects($this->any())
            ->method('exists')
            ->willReturn(false);
        $session = new UserSession($user, $client, $store, $tokenCachePool);
        $authenticatedClient = new AuthenticatedClient($client, $session, $account);
        $manager = DataServiceManager::basicDiscovery();
        $service = $manager->getDataService('Media Data Service', 'Media', '1.10');
        $listener = new NotificationListener($authenticatedClient, $service, 'unit-tests');
        /** @var \Lullabot\Mpx\DataService\Notification[] $notifications */
        $notifications = $listener->listen(98765)->wait();
        $this->assertCount(2, $notifications);

        foreach ($notifications as $index => $notification) {
            // MPX IDs start at 1, so we bump up this index.
            ++$index;

            $this->assertEquals($index, $notification->getId());

            1 == $index ? $method = 'put' : $method = 'post';
            $this->assertEquals($method, $notification->getMethod());
            $this->assertEquals('Media', $notification->getType());

            /** @var Media $media */
            $media = $notification->getEntry();
            $this->assertInstanceOf(Media::class, $media);

            $this->assertEquals('http://data.media.theplatform.com/media/data/Media/'.$index, $media->getId());
            $this->assertEquals('http://access.auth.theplatform.com/data/Account/'.$index, $media->getOwnerId());
            $this->assertEquals('https://identity.auth.theplatform.com/idm/data/User/service/'.$index, $media->getUpdatedByUserId());
            $this->assertEquals(1_521_790_000 + ($index - 1) * 1000, $media->getUpdated()->format('U'));
        }
    }
}
