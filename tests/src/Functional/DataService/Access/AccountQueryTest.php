<?php

namespace Lullabot\Mpx\Tests\Functional\DataService\Account;

use Lullabot\Mpx\AuthenticatedClient;
use Lullabot\Mpx\DataService\DataObjectFactory;
use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\DataService\ObjectListQuery;
use Lullabot\Mpx\Tests\Functional\FunctionalTestBase;
use Psr\Http\Message\UriInterface;

/**
 * Tests loading Account objects.
 */
class AccountQueryTest extends FunctionalTestBase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        // Override the client to not include an account context, since the
        // access data service does not support it.
        $this->authenticatedClient = new AuthenticatedClient(
            $this->client,
            $this->userSession
        );
    }

    /**
     * Test loading two Account objects.
     */
    public function testQueryAccount()
    {
        $manager = DataServiceManager::basicDiscovery();
        $dof = new DataObjectFactory($manager->getDataService('Access Data Service', 'Account', '1.0'), $this->authenticatedClient);
        $filter = new ObjectListQuery();
        $filter->getRange()
            ->setStartIndex(1)
            ->setEndIndex(2);
        $results = $dof->select($filter);

        foreach ($results as $index => $result) {
            $this->assertInstanceOf(UriInterface::class, $result->getId());

            // Loading the object by itself.
            $reload = $dof->load($result->getId());
            $item = $reload->wait();

            // We need to override the JSON strings. While the strings may be
            // functionally identical, the namespace prefixes in the responses
            // can change between requests.
            $result->setJson('{}');
            $item->setJson('{}');
            $this->assertEquals($result, $item);
            if ($index + 1 > 2) {
                break;
            }
        }
    }
}
