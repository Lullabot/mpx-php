<?php

namespace Lullabot\Mpx\Tests\Functional\Service\AccessManagement;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Concat\Http\Middleware\Logger;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\MessageFormatter;
use Lullabot\Mpx\Client;
use Lullabot\Mpx\Service\AccessManagement\ResolveAllUrls;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\TokenCachePool;
use Lullabot\Mpx\User;
use Namshi\Cuzzle\Middleware\CurlFormatterMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Test resolving URLs with a live API call.
 */
class ResolveAllUrlsTest extends TestCase
{
    /**
     * Execute a resolveAllUrls() call.
     */
    public function testResolve()
    {
        $username = getenv('MPX_USERNAME');
        $password = getenv('MPX_PASSWORD');

        if (empty($username) || empty($password)) {
            $this->markTestSkipped('MPX_USER and MPX_PASSWORD must be defined as environment variables or in phpunit.xml for functional tests.');
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

        $client = new Client(new GuzzleClient($config));
        $user = new User($username, $password);
        $session = new UserSession($client, $user, new TokenCachePool(new ArrayCachePool()), new NullLogger());

        /** @var ResolveAllUrls $resolved */
        $resolved = ResolveAllUrls::load($session, 'Media Data Service')->wait();
        $this->assertInternalType('string', $resolved->getService());
    }
}
