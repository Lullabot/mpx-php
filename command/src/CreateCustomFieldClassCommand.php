<?php

namespace Lullabot\Mpx\Command;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Lullabot\Mpx\AuthenticatedClient;
use Lullabot\Mpx\Client;
use Lullabot\Mpx\DataService\ObjectListQuery;
use Lullabot\Mpx\DataService\CustomFieldInterface;
use Lullabot\Mpx\DataService\DataObjectFactory;
use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\DataService\Field;
use Lullabot\Mpx\Service\IdentityManagement\User;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\TokenCachePool;
use Nette\PhpGenerator\PhpNamespace;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Lock\Store\FlockStore;

/**
 * Command to generate classes for custom mpx fields.
 */
class CreateCustomFieldClassCommand extends Command {

    /**
     * Maps custom mpx field types to PHP datatypes.
     */
    CONST TYPE_MAP = [
        'Boolean' => 'bool',
        'Date' => '\\' . \DateTime::class,
        'DateTime' => '\\' . \DateTime::class,
        'Duration' => 'int',
        'Decimal' => 'float',
        'Image' => '\\' . UriInterface::class,
        'Integer' => 'int',
        'Link' => '\\' . UriInterface::class,
        'String' => 'string',
        'Time' => '\\' . \DateTime::class,
        'URI' => '\\' . UriInterface::class,
    ];

    protected function configure() {
        $help = <<<EOD
This command generates a PHP class for a custom field implementation.

An mpx username and password is required. They will be requested interactively,
or MPX_USERNAME and MPX_PASSWORD environment variables will be used.

As mpx provides no way to infer a reasonable class name, each class is named
sequentially with the expectation that it will be renamed later. One file will
be generated for each namespace of custom fields that is detected. Files will
be written to the current working directory.
EOD;
        $this->setName('mpx:create-custom-field')
            ->setDescription('This command helps to create a custom field class.')
            ->setHelp($help)
            ->addUsage("mpx:create-custom-field 'Lullabot\\Mpx\\CustomField' 'Media Data Service' 'Media' '1.10'")
            ->addArgument('namespace', InputArgument::REQUIRED, 'The fully-qualified class name to generate. Do not include the leading slash, and wrap the class name in single-quotes to handle shell escaping.')
            ->addArgument('data-service', InputArgument::REQUIRED, "The data service to generate the class for, such as 'Media Data Service'.")
            ->addArgument('data-object', InputArgument::REQUIRED, "The object to generate the class for, such as 'Media'")
            ->addArgument('schema', InputArgument::REQUIRED, "The schema version of the object, such as '1.10'");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        if (!extension_loaded('intl')) {
            throw new \RuntimeException('The intl extension is required to run this command.');
        }

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
        $client = new Client(new \GuzzleHttp\Client($config));
        $store = new FlockStore();
        $user = new User($username, $password);
        $userSession = new UserSession($user, $client, $store, new TokenCachePool(new ArrayCachePool()));
        $authenticatedClient = new AuthenticatedClient(
            $client,
            $userSession
        );
        $manager = DataServiceManager::basicDiscovery();
        $dataService = $manager->getDataService($input->getArgument('data-service'), $input->getArgument('data-object'), $input->getArgument('schema'));
        $dof = new DataObjectFactory($dataService->getAnnotation()->getFieldDataService(), $authenticatedClient);
        $filter = new ObjectListQuery();
        /** @var Field[] $results */
        $results = $dof->select($filter);

        $namespaceClasses = [];
        $classNames = [];

        $nf = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
        $output->writeln('Generating classes for all custom fields:');
        foreach ($results as $field) {
            $output->writeln($field->getNamespace().':'.$field->getFieldName());

            // The response does not contain complete field data, so we reload it.
            /** @var Field $field */
            $field = $dof->load($field->getId())->wait();

            $mpxNamespace = (string)$field->getNamespace();
            if (!isset($namespaceClasses[$mpxNamespace])) {
                $className = 'CustomFieldClass'.ucfirst(str_replace('-', '', $nf->format(count($namespaceClasses) + 1)));
                $namespace = new PhpNamespace($input->getArgument('namespace'));
                $class = $namespace->addClass($className);
                $classNames[$mpxNamespace] = $className;
                $namespaceClasses[$mpxNamespace] = $namespace;
                $class->addImplement(CustomFieldInterface::class);

                $class->addComment('@\Lullabot\Mpx\DataService\Annotation\CustomField(');
                $class->addComment('    namespace="'.$mpxNamespace.'",');
                $class->addComment('    service="'.$input->getArgument('data-service').'",');
                $class->addComment('    objectType="'.$input->getArgument('data-object').'",');
                $class->addComment(')');
            }
            else {
                $namespace = $namespaceClasses[$mpxNamespace];
                $class = $namespace->getClasses()[$classNames[$mpxNamespace]];
            }

            $property = $class->addProperty($field->getFieldName());
            $property->setVisibility('protected');
            if (!empty($field->getDescription())) {
                $property->setComment($field->getDescription());
                $property->addComment('');
            }
            $dataType = static::TYPE_MAP[$field->getDataType()];
            if ('Single' != $field->getDataStructure()) {
                $dataType .= '[]';
            }
            $property->addComment('@var '.$dataType);

            $get = $class->addMethod('get' . ucfirst($property->getName()));
            $get->setVisibility('public');
            if (!empty($field->getDescription())) {
                $get->addComment('Returns ' . lcfirst($field->getDescription()));
                $get->addComment('');
            }
            $get->addComment('@return ' . $dataType);

            // If the property is a typed array, PHP will only let us use
            // array in the return typehint.
            $substr = substr($dataType, -2);
            switch ($substr) {
                case '[]':
                    $get->setReturnType('array');
                    break;
                default:
                    $get->setReturnType($dataType);
                    if (in_array($dataType, ['int', 'float', 'string', 'bool'])) {
                        $get->setReturnNullable(true);
                    }
                    break;
            }

            $get->addBody('return $this->' . $field->getFieldName() . ';');

            // Add a set method for the property.
            $set = $class->addMethod('set' . ucfirst($property->getName()));
            $set->setVisibility('public');
            if (!empty($field->getDescription())) {
                $set->addComment('Set ' . lcfirst($field->getDescription()));
                $set->addComment('');
            }
            $set->addComment('@param ' . $field->getDataType());
            $set->addParameter($field->getFieldName());
            $set->addBody('$this->' . $field->getFieldName() . ' = ' . '$' . $field->getFieldName() . ';');
        }

        foreach ($namespaceClasses as $namespaceClass) {
            $array = $namespaceClass->getClasses();
            $classType = reset($array);
            $classFile = new StreamOutput(fopen($classType->getName().'.php', 'w'));
            $classFile->write("<?php\n\n");
            $classFile->write((string) $namespaceClass);
            fclose($classFile->getStream());
        }
    }
}
