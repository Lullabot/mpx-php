<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use Lullabot\Mpx\DataService\Media\Media;
use Lullabot\Mpx\DataService\Notification;
use Lullabot\Mpx\DataService\NotificationTypeExtractor;

/**
 * Tests the Notification object.
 *
 * @covers \Lullabot\Mpx\DataService\Notification
 */
class NotificationTest extends ObjectTestBase
{
    protected $class = Notification::class;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $notificationExtractor = new NotificationTypeExtractor();
        $notificationExtractor->setClass(Media::class);
        $this->loadFixture('notification.json', $notificationExtractor);
    }

    /**
     * Test get / set methods.
     *
     * @param string $field The field on Notification to test.
     *
     * @dataProvider getSetMethods
     */
    public function testGetSet(string $field)
    {
        /** @var Notification[] $notifications */
        $notifications = $this->serializer->deserialize(json_encode($this->decoded), Notification::class.'[]', 'json');
        $method = 'get'.ucfirst($field);
        foreach ($notifications as $index => $notification) {
            if ('entry' == $field) {
                $this->assertInstanceOf(Media::class, $notification->$method());
            } else {
                $expected = $this->decoded[$index][$field];
                $this->assertEquals($expected, $notification->$method());
            }
        }
    }

    public function testIsSyncResponse()
    {
        /** @var Notification[] $notifications */
        $notifications = $this->serializer->deserialize('[{"id": 12345 }]', Notification::class.'[]', 'json');
        $this->assertCount(1, $notifications);
        $this->assertTrue($notifications[0]->isSyncResponse());
    }

    public function testIsNotSyncResponse()
    {
        /** @var Notification[] $notifications */
        $notifications = $this->serializer->deserialize('[{"id": 12345, "method": "get" }]', Notification::class.'[]', 'json');
        $this->assertCount(1, $notifications);
        $this->assertFalse($notifications[0]->isSyncResponse());
    }
}
