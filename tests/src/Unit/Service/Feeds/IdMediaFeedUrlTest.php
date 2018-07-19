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
