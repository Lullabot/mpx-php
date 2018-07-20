<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\Feeds;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\DateTime\ConcreteDateTime;
use Lullabot\Mpx\DataService\DataServiceExtractor;
use Lullabot\Mpx\DataService\Feeds\FeedConfig;
use Lullabot\Mpx\Tests\Unit\DataService\ObjectTestBase;

/**
 * Test the FeedConfig data object.
 *
 * @covers \Lullabot\Mpx\DataService\Feeds\FeedConfig
 */
class FeedConfigTest extends ObjectTestBase
{
    protected $class = FeedConfig::class;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $dataServiceExtractor = new DataServiceExtractor();
        $dataServiceExtractor->setClass($this->class);
        $this->loadFixture('feedconfig-object.json', $dataServiceExtractor);
    }

    /**
     * Test get / set methods.
     *
     * @param string $field    The field on FeedConfig to test.
     * @param mixed  $expected (optional) Override the assertion if data is converted, such as with timestamps.
     *
     * @dataProvider getSetMethods
     */
    public function testGetSet(string $field, $expected = null)
    {
        $this->assertObjectClass($this->class, $field, $expected);
    }

    public function getSetMethods()
    {
        $tests = parent::getSetMethods();

        $tests['added'] = ['added', new ConcreteDateTime(\DateTime::createFromFormat('U.u', '1342817557.000'))];
        $tests['updated'] = ['updated', new ConcreteDateTime(\DateTime::createFromFormat('U.u', '1342817557.000'))];
        $tests['adPolicyId'] = ['adPolicyId', new Uri()];

        unset($tests['sortKeys']);
        unset($tests['subFeeds']);

        return $tests;
    }
}
