<?php

namespace Lullabot\Mpx\Command;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Concat\Http\Middleware\Logger;
use GuzzleHttp\MessageFormatter;
use Lullabot\Mpx\AuthenticatedClient;
use Lullabot\Mpx\Client;
use Lullabot\Mpx\DataService\DataObjectFactory;
use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\Service\IdentityManagement\User;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\TokenCachePool;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Lock\Store\FlockStore;

abstract class MpxCommandBase extends Command
{
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return DataObjectFactory
     */
    protected function getDataObjectFactory(InputInterface $input, OutputInterface $output): DataObjectFactory
    {
        $authenticatedClient = $this->getAuthenticatedClient($input, $output);
        $manager = DataServiceManager::basicDiscovery();
        $dataService = $manager->getDataService(
            $input->getArgument('data-service'),
            $input->getArgument('data-object'),
            $input->getArgument('schema')
        );
        $dof = new DataObjectFactory($dataService->getAnnotation()
            ->getFieldDataService(), $authenticatedClient);

        return $dof;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \Lullabot\Mpx\AuthenticatedClient
     */
    protected function getAuthenticatedClient(InputInterface $input, OutputInterface $output): \Lullabot\Mpx\AuthenticatedClient
    {
        $helper = $this->getHelper('question');
        if (!$username = getenv('MPX_USERNAME')) {
            $question = new Question('Enter the mpx user name, such as mpx/you@example.com: ');
            $username = $helper->ask($input, $output, $question);
        }
        if (!$password = getenv('MPX_PASSWORD')) {
            $question = new Question('Enter the mpx password: ');
            $question->setHidden(true)
                ->setHiddenFallback(false);
            $password = $helper->ask($input, $output, $question);
        }

        $config = Client::getDefaultConfiguration();

        $responseLogger = new Logger(new ConsoleLogger($output));
        $responseLogger->setLogLevel(LogLevel::DEBUG);
        $responseLogger->setFormatter(new MessageFormatter(MessageFormatter::DEBUG));
        $handler = $config['handler'];
        $handler->after('cookies', $responseLogger);

        $client = new Client(new \GuzzleHttp\Client($config));

        $store = new FlockStore();
        $user = new User($username, $password);
        $userSession = new UserSession($user, $client, $store, new TokenCachePool(new ArrayCachePool()));
        $authenticatedClient = new AuthenticatedClient(
            $client,
            $userSession
        );

        return $authenticatedClient;
    }
}
