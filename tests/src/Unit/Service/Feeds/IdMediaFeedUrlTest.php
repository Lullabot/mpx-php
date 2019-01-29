<?php

namespace Lullabot\Mpx\Tests\Unit\Service\Feeds;

use Lullabot\Mpx\DataService\Access\Account;
use Lullabot\Mpx\DataService\Feeds\FeedConfig;
use Lullabot\Mpx\Service\Feeds\IdMediaFeedUrl;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lullabot\Mpx\Service\Feeds\IdMediaFeedUrl
 */
class IdMediaFeedUrlTest extends TestCase
{
    public function testEmptyIds()
    {
        $account = new Account();
        $account->setPid('account-pid');
        $feedConfig = new FeedConfig();
        $feedConfig->setPid('feed-pid');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one ID must be specified');
        new IdMediaFeedUrl($account, $feedConfig, []);
    }

    public function testInvalidIds()
    {
        $account = new Account();
        $account->setPid('account-pid');
        $feedConfig = new FeedConfig();
        $feedConfig->setPid('feed-pid');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('All IDs must be integers');
        new IdMediaFeedUrl($account, $feedConfig, [34, '45']);
    }

    public function testToUri()
    {
        $account = new Account();
        $account->setPid('account-pid');
        $feedConfig = new FeedConfig();
        $feedConfig->setPid('feed-pid');
        $url = new IdMediaFeedUrl($account, $feedConfig, [1, 2]);
        $this->assertEquals([1, 2], $url->getIds());
        $this->assertEquals('https://feed.media.theplatform.com/f/account-pid/feed-pid/1,2', (string) $url->toUri());
    }
}
