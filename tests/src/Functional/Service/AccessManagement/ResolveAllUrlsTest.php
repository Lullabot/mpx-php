<?php

namespace Lullabot\Mpx\Tests\Functional\Service\AccessManagement;

use Cache\Adapter\PHPArray\ArrayCachePool;
use GuzzleHttp\Client as GuzzleClient;
use Lullabot\Mpx\Client;
use Lullabot\Mpx\Service\AccessManagement\ResolveAllUrls;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\TokenCachePool;
use Lullabot\Mpx\User;
use Namshi\Cuzzle\Middleware\CurlFormatterMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;

class ResolveAllUrlsTest extends TestCase
{

    public function testResolve()
    {
        $username = getenv('MPX_USERNAME');
        $password = getenv('MPX_PASSWORD');

        if (empty($username) || empty($password)) {
            $this->markTestSkipped('MPX_USER and MPX_PASSWORD must be defined as environment variables or in phpunit.xml for functional tests.');
        }

        $config = Client::getDefaultConfiguration();

        if (getEnv('MPX_LOG_CURL')) {
            $output = new ConsoleOutput();
            $output->setVerbosity(ConsoleOutput::VERBOSITY_DEBUG);
            $cl = new ConsoleLogger($output);
            /** @var $handler \GuzzleHttp\HandlerStack */
            $handler = $config['handler'];
            $handler->after('cookies', new CurlFormatterMiddleware($cl));
        }

        $client = new Client(new GuzzleClient($config));
        $user = new User($username, $password);
        $session = new UserSession($client, $user, new TokenCachePool(new ArrayCachePool()), new NullLogger());

        /** @var ResolveAllUrls $resolved */
        $resolved = ResolveAllUrls::load($session, 'Media Data Service')->wait();
        $this->assertInternalType('string', $resolved->getService());
    }
}
