<?php

namespace Lullabot\Mpx\Tests\Functional;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Concat\Http\Middleware\Logger;
use GuzzleHttp\MessageFormatter;
use Lullabot\Mpx\AuthenticatedClient;
use Lullabot\Mpx\Client;
use Lullabot\Mpx\DataService\Access\Account;
use Lullabot\Mpx\Service\IdentityManagement\User;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\TokenCachePool;
use Namshi\Cuzzle\Middleware\CurlFormatterMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Lock\StoreInterface;

abstract class FunctionalTestBase extends TestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var \Lullabot\Mpx\Service\IdentityManagement\UserSession
     */
    protected $userSession;

    /**
     * @var \Lullabot\Mpx\AuthenticatedClient
     */
    protected $session;

    /**
     * The MPX account.
     *
     * @var Account
     */
    protected $account;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $username = getenv('MPX_USERNAME');
        $password = getenv('MPX_PASSWORD');
        $account = getenv('MPX_ACCOUNT');

        if (empty($username) || empty($password) || empty($account)) {
            $this->markTestSkipped(
                'MPX_USER, MPX_PASSWORD, and MPX_ACCOUNT must be defined as environment variables or in phpunit.xml for functional tests.'
            );
        }

        $config = Client::getDefaultConfiguration();

        if (getenv('MPX_LOG_CURL')) {
            $output = new ConsoleOutput();
            $output->setVerbosity(ConsoleOutput::VERBOSITY_DEBUG);
            $cl = new ConsoleLogger($output);
            /** @var $handler \GuzzleHttp\HandlerStack */
            $handler = $config['handler'];
            $handler->after('cookies', new CurlFormatterMiddleware($cl));

            $responseLogger = new Logger($cl);
            $responseLogger->setLogLevel(LogLevel::DEBUG);
            $responseLogger->setFormatter(new MessageFormatter(MessageFormatter::DEBUG));
            $handler->after('cookies', $responseLogger);
        }

        $this->client = new Client(new \GuzzleHttp\Client($config));

        // Since this is a single thread within a test, we know it's impossible
        // for the lock to fail so we can stub out the entire class.
        /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder(StoreInterface::class)->getMock();

        $user = new User($username, $password);
        $this->userSession = new UserSession($user, $this->client, $store, new TokenCachePool(new ArrayCachePool()), new NullLogger());
        $this->session = new AuthenticatedClient(
            $this->client,
            $this->userSession
        );

        $this->account = new Account();
        $this->account->setId($account);
    }
}
