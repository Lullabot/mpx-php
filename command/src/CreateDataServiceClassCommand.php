<?php

namespace Lullabot\Mpx\Command;

use Lullabot\Mpx\CreateKeyInterface;
use Nette\PhpGenerator\PhpNamespace;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDataServiceClassCommand extends Command {

    CONST TYPE_MAP = [
        'String' => 'string',
        'Boolean' => 'boolean',
        'Long' => 'int',
        'URI' => '\\' . UriInterface::class,
        'Map' => 'array',
        'DateTime' => '\\' . \DateTime::class,
    ];

    protected function configure() {
        $this->setName('mpx:create-data-service')
            ->setDescription('This command helps to create a data service class from a CSV via stdin.')
            ->setHelp("This command generates a PHP class from a CSV copied from MPX's documentation. Create the CSV by copying the fields table from an object and converting it to a CSV using a spreadsheet. The CSV is read through STDIN. \n\nhttps://docs.theplatform.com/help/media-media-object is a good example of the table this command expects.")
            ->addUsage('Lullabot\Mpx\DataService\Media\Media < command/csv/media-object.csv')
            ->addArgument('fully-qualified-class-name', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'The fully-qualified class name to generate. Do not include the leading slash, and wrap the class name in single-quotes to handle shell escaping.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $handle = fopen('php://stdin', 'r');
        $index = 0;

        $output->write("<?php\n\n");

        // Extract the containing class namespace and the class name.
        $parts = explode('\\', $input->getArgument('fully-qualified-class-name'));
        $namespace = new PhpNamespace(implode('\\', array_slice($parts, 0, -1)));
        $class = $namespace->addClass(end($parts));

        $class->addImplement(CreateKeyInterface::class);

        // Loop over each row, which corresponds to each property.
        while (!feof($handle)) {
            $row = fgetcsv($handle);
            if ($index == 0) {
                $index++;
                continue;
            }
            $index++;

            list($field_name, $attributes, $data_type, $description) = $row;

            // Map MPX documentation datatypes to PHP datatypes.
            foreach (static::TYPE_MAP as $search => $replace) {
                $data_type = str_replace($search, $replace, $data_type);
            }

            // Add the protected property.
            $property = $class->addProperty($field_name);
            $property->setVisibility('protected');
            $property->setComment($description);

            $property->addComment('');
            $property->addComment('@var ' . $data_type);

            // Add a get method for the property.
            $get = $class->addMethod('get' . ucfirst($property->getName()));
            $get->setVisibility('public');
            $get->addComment('Returns ' . $description);
            $get->addComment('');
            $get->addComment('@return ' . $data_type);

            // If the property is a typed array, PHP will only let us use
            // array in the return typehint.
            $substr = substr($data_type, -2);
            switch ($substr) {
                case '[]':
                    $get->setReturnType('array');
                    break;
                default:
                    $get->setReturnType($data_type);
                    break;
            }

            $get->addBody('return $this->' . $field_name . ';');

            // Add a set method for the property.
            $set = $class->addMethod('set' . ucfirst($property->getName()));
            $set->setVisibility('public');
            $set->addComment('Set ' . $description);
            $set->addComment('');
            $set->addComment('@param ' . $data_type);
            $set->addParameter($field_name);
            $set->addBody('$this->' . $field_name . ' = ' . '$' . $field_name . ';');
        }

        $output->write((string) $namespace);
        fclose($handle);

    }
}
