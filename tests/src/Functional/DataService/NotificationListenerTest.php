<?php

namespace Lullabot\Mpx\Tests\Functional\DataService;

use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\DataService\Media\Media;
use Lullabot\Mpx\DataService\NotificationListener;
use Lullabot\Mpx\Tests\Functional\FunctionalTestBase;

class NotificationListenerTest extends FunctionalTestBase
{
    /**
     * Tests listening for two Media notifications.
     */
    public function testListen()
    {
        $manager = DataServiceManager::basicDiscovery();
        $service = $manager->getDataService('Media Data Service', 'Media', '1.10');
        $listener = new NotificationListener($this->authenticatedClient, $service, 'lullabot/mpx-php');
        /** @var \Lullabot\Mpx\DataService\Notification[] $notifications */
        $notifications = $listener->listen(-1, 2)->wait();
        $this->assertCount(2, $notifications);
        foreach ($notifications as $notification) {
            $this->assertInstanceOf(Media::class, $notification->getEntry());
        }
    }
}
