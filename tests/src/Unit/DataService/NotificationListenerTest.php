<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Lullabot\Mpx\AuthenticatedClient;
use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\DataService\Media\Media;
use Lullabot\Mpx\DataService\NotificationListener;
use Lullabot\Mpx\Service\IdentityManagement\User;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\Tests\JsonResponse;
use Lullabot\Mpx\Tests\MockClientTrait;
use Lullabot\Mpx\TokenCachePool;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\StoreInterface;

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
        $id = rand(1, getrandmax());
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'resolveAllUrls.json'),
            new JsonResponse(200, [], [
                [
                    'id' => $id,
                ],
            ]),
        ]);
        $user = new User('username', 'password');
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());
        /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)->getMock();
        $session = new UserSession($user, $client, $store, $tokenCachePool);
        $authenticatedClient = new AuthenticatedClient($client, $session);
        $manager = DataServiceManager::basicDiscovery();
        $service = $manager->getDataService('Media Data Service', 'Media', '1.10');
        $listener = new NotificationListener($authenticatedClient, $service, 'unit-tests');
        $last = $listener->sync()->wait();
        $this->assertEquals($id, $last);
    }

    /**
     * Test listening for notifications.
     *
     * @covers ::__construct
     * @covers ::listen
     */
    public function testListen()
    {
        $client = $this->getMockClient([
            new JsonResponse(200, [], 'signin-success.json'),
            new JsonResponse(200, [], 'resolveAllUrls.json'),
            new JsonResponse(200, [], 'notification.json'),
        ]);
        $user = new User('username', 'password');
        $tokenCachePool = new TokenCachePool(new ArrayCachePool());
        /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)->getMock();
        $session = new UserSession($user, $client, $store, $tokenCachePool);
        $authenticatedClient = new AuthenticatedClient($client, $session);
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

            (1 == $index ? $method = 'put' : $method = 'post');
            $this->assertEquals($method, $notification->getMethod());
            $this->assertEquals('Media', $notification->getType());

            /** @var Media $media */
            $media = $notification->getEntry();
            $this->assertInstanceOf(Media::class, $media);

            $this->assertEquals('http://data.media.theplatform.com/media/data/Media/'.$index, $media->getId());
            $this->assertEquals('http://access.auth.theplatform.com/data/Account/'.$index, $media->getOwnerId());
            $this->assertEquals('https://identity.auth.theplatform.com/idm/data/User/service/'.$index, $media->getUpdatedByUserId());
            $this->assertEquals(1521790000 + ($index - 1) * 1000, $media->getUpdated()->format('U'));
        }
    }
}
