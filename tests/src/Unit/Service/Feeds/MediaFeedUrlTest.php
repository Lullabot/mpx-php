<?php

namespace Lullabot\Mpx\Tests\Unit\Service\Feeds;

use Lullabot\Mpx\DataService\Access\Account;
use Lullabot\Mpx\DataService\Feeds\FeedConfig;
use Lullabot\Mpx\DataService\Feeds\SubFeed;
use Lullabot\Mpx\Service\Feeds\MediaFeedUrl;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lullabot\Mpx\Service\Feeds\MediaFeedUrl
 */
class MediaFeedUrlTest extends TestCase
{
    /**
     * @param string $property
     * @param mixed  $value
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($get, $set, $value)
    {
        $account = new Account();
        $account->setPid('account-pid');
        $feedConfig = new FeedConfig();
        $feedConfig->setPid('feed-pid');

        $url = new MediaFeedUrl($account, $feedConfig);
        $url->$set($value);
        $this->assertEquals($value, $url->$get());
    }

    public function getSetDataProvider()
    {
        $subfeed = new SubFeed();
        $subfeed->setFeedType('Category');

        return [
            'seo terms' => ['getSeoTerms', 'setSeoTerms', [random_bytes(8), random_bytes(8)]],
            'feed type' => ['getFeedType', 'setFeedType', $subfeed],
            'returns feed' => ['isReturnsFeed', 'setReturnsFeed', true],
            'does not return feed' => ['isReturnsFeed', 'setReturnsFeed', false],
        ];
    }

    public function testToString()
    {
        $account = new Account();
        $account->setPid('account-pid');
        $feedConfig = new FeedConfig();
        $feedConfig->setPid('feed-pid');

        $url = new MediaFeedUrl($account, $feedConfig);
        $this->assertEquals('https://feed.media.theplatform.com/f/account-pid/feed-pid', (string) $url);
    }

    public function testToUri()
    {
        $account = new Account();
        $account->setPid('account-pid');
        $feedConfig = new FeedConfig();
        $feedConfig->setPid('feed-pid');

        $url = new MediaFeedUrl($account, $feedConfig);
        $uri = $url->toUri();
        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('feed.media.theplatform.com', $uri->getHost());
        $this->assertEquals('/f/account-pid/feed-pid', $uri->getPath());
    }
}
