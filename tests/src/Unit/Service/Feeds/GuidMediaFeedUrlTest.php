<?php

namespace Lullabot\Mpx\Tests\Unit\Service\Feeds;

use Lullabot\Mpx\DataService\Access\Account;
use Lullabot\Mpx\DataService\Feeds\FeedConfig;
use Lullabot\Mpx\Service\Feeds\GuidMediaFeedUrl;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lullabot\Mpx\Service\Feeds\GuidMediaFeedUrl
 */
class GuidMediaFeedUrlTest extends TestCase
{
    public function testToUri()
    {
        $account = new Account();
        $account->setPid('account-pid');
        $feedConfig = new FeedConfig();
        $feedConfig->setPid('feed-pid');
        $url = new GuidMediaFeedUrl($account, $feedConfig, ['first', 'second']);
        $this->assertEquals(['first', 'second'], $url->getGuids());
        $this->assertEquals('https://feed.media.theplatform.com/f/account-pid/feed-pid/guid/-/first,second', (string) $url->toUri());
    }
}
