<?php

namespace Lullabot\Mpx\Command;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\DataObjectFactory;
use Lullabot\Mpx\DataService\DataServiceManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Execute a GET request on an mpx data service object.
 */
class GetCommand extends MpxCommandBase
{
    protected function configure()
    {
        parent::configure();
        $help = <<<EOD
This command loads an mpx data service object.

An mpx username and password is required. They will be requested interactively,
or MPX_USERNAME, and MPX_PASSWORD environment variables will be used.

If debug logging is enabled (-vvv) then each request and response will be
printed to the console.
EOD;
        $this->setName('mpx:get')
            ->setDescription('This command GETs an mpx object by ID.')
            ->setHelp($help)
            ->addUsage('mpx:get http://data.media.theplatform.com/media/data/Media/12345')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'The full ID of the mpx object to load.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $this->getAuthenticatedClient($input, $output);

        $id = new Uri($input->getArgument('id'));
        $parts = explode('/', $id->getPath());
        if ('data' != $parts[2]) {
            throw new \RuntimeException('The URL is not a data service URL');
        }
        $s = $parts[3];
        $service_name = ucfirst($s).' Data Service';

        $manager = DataServiceManager::basicDiscovery();
        $service = null;
        $services = $manager->getDataServices();
        if (isset($services[$service_name][$s])) {
            $service = reset($services[$service_name][$s]);
        } else {
            throw new \RuntimeException('No data service was found for the URL');
        }

        $dof = new DataObjectFactory($service->getAnnotation()->getFieldDataService(), $client);
        $loaded = $dof->load($id)->wait();
        $output->write(var_export($loaded, true));
    }
}
