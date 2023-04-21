<?php

namespace Lullabot\Mpx\Command;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\DataService\CustomFieldInterface;
use Lullabot\Mpx\DataService\DataObjectFactory;
use Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface;
use Lullabot\Mpx\DataService\DateTime\NullDateTime;
use Lullabot\Mpx\DataService\Field;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Command to generate classes for custom mpx fields.
 */
class CreateCustomFieldClassCommand extends ClassGeneratorBase
{
    /**
     * @var PhpNamespace[]
     */
    protected $namespaceClasses = [];

    /**
     * @var string[]
     */
    protected $classNames = [];

    protected function configure()
    {
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
            ->addArgument(
                'namespace',
                InputArgument::REQUIRED,
                'The fully-qualified class name to generate. Do not include the leading slash, and wrap the class name in single-quotes to handle shell escaping.'
            )
            ->addArgument(
                'data-service',
                InputArgument::REQUIRED,
                "The data service to generate the class for, such as 'Media Data Service'."
            )
            ->addArgument(
                'data-object',
                InputArgument::REQUIRED,
                "The object to generate the class for, such as 'Media'"
            )
            ->addArgument('schema', InputArgument::REQUIRED, "The schema version of the object, such as '1.10'");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!\extension_loaded('intl')) {
            throw new \RuntimeException('The intl extension is required to run this command.');
        }

        $dof = $this->getDataObjectFactory($input, $output);
        /** @var Field[] $results */
        $results = $dof->select();

        $output->writeln('Generating classes for all custom fields:');
        foreach ($results as $field) {
            $output->writeln($field->getNamespace().':'.$field->getFieldName());
            $this->addField($input, $dof, $field);
        }

        foreach ($this->namespaceClasses as $namespaceClass) {
            $array = $namespaceClass->getClasses();
            $classType = reset($array);
            $classFile = new StreamOutput(fopen($classType->getName().'.php', 'wb'));
            $classFile->write("<?php\n\n");
            $classFile->write((string) $namespaceClass);
            fclose($classFile->getStream());
        }
    }

    /**
     * @param InputInterface $input
     * @param                $dof
     * @param                $field
     */
    private function addField(InputInterface $input, DataObjectFactory $dof, Field $field): void
    {
        // The response does not contain complete field data, so we reload it.
        $field = $dof->load($field->getId())->wait();

        $mpxNamespace = (string) $field->getNamespace();
        /** @var PhpNamespace $namespace */
        /** @var ClassType $class */
        [$namespace, $class] = $this->getClass($input, $mpxNamespace);

        $this->addProperty($class, $field);
        $dataType = $this->getPhpDataType($field);

        $get = $class->addMethod('get'.ucfirst((string) $field->getFieldName()));
        $get->setVisibility('public');
        if (!empty($field->getDescription())) {
            $get->addComment('Returns '.lcfirst((string) $field->getDescription()));
            $get->addComment('');
        }
        $get->addComment('@return '.$dataType);

        // If the property is a typed array, PHP will only let us use
        // array in the return typehint.
        $this->setReturnType($get, $dataType);

        if ($dataType == '\\'.DateTimeFormatInterface::class) {
            $namespace->addUse(NullDateTime::class);
            $get->addBody('if (!$this->'.$field->getFieldName().') {');
            $get->addBody('    return new NullDateTime();');
            $get->addBody('}');
        }

        if ($dataType == '\\'.UriInterface::class) {
            $namespace->addUse(Uri::class);
            $get->addBody('if (!$this->'.$field->getFieldName().') {');
            $get->addBody('    return new Uri();');
            $get->addBody('}');
        }
        $get->addBody('return $this->'.$field->getFieldName().';');

        // Add a set method for the property.
        $set = $class->addMethod('set'.ucfirst((string) $field->getFieldName()));
        $set->setVisibility('public');
        if (!empty($field->getDescription())) {
            $set->addComment('Set '.lcfirst((string) $field->getDescription()));
            $set->addComment('');
        }
        $set->addComment('@param '.$dataType);
        $parameter = $set->addParameter($field->getFieldName());

        $this->setTypeHint($parameter, $dataType);
        $set->addBody('$this->'.$field->getFieldName().' = '.'$'.$field->getFieldName().';');
    }

    /**
     * @param InputInterface $input
     * @param string         $mpxNamespace
     *
     * @return array
     */
    private function getClass(InputInterface $input, string $mpxNamespace): array
    {
        if (!isset($this->namespaceClasses[$mpxNamespace])) {
            $nf = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
            $className = 'CustomFieldClass'.ucfirst(
                    str_replace('-', '', $nf->format(\count($this->namespaceClasses) + 1))
                );
            $namespace = new PhpNamespace($input->getArgument('namespace'));
            $class = $namespace->addClass($className);
            $this->classNames[$mpxNamespace] = $className;
            $this->namespaceClasses[$mpxNamespace] = $namespace;
            $class->addImplement(CustomFieldInterface::class);

            $class->addComment('@\Lullabot\Mpx\DataService\Annotation\CustomField(');
            $class->addComment('    namespace="'.$mpxNamespace.'",');
            $class->addComment('    service="'.$input->getArgument('data-service').'",');
            $class->addComment('    objectType="'.$input->getArgument('data-object').'",');
            $class->addComment(')');
        } else {
            $namespace = $this->namespaceClasses[$mpxNamespace];
            $class = $namespace->getClasses()[$this->classNames[$mpxNamespace]];
        }

        return [$namespace, $class];
    }

    /**
     * Add a property to a class.
     *
     * @param ClassType $class
     * @param Field     $field
     */
    private function addProperty(ClassType $class, Field $field)
    {
        $property = $class->addProperty($field->getFieldName());
        $property->setVisibility('protected');
        if (!empty($field->getDescription())) {
            $property->setComment($field->getDescription());
            $property->addComment('');
        }
        $dataType = $this->getPhpDataType($field);
        $property->addComment('@var '.$dataType);
        if ($this->isCollectionType($dataType)) {
            $property->setValue([]);
        }
    }

    /**
     * Get the PHP data type for a field, including mapping to arrays.
     *
     * @param Field $field
     *
     * @return string
     */
    private function getPhpDataType(Field $field): string
    {
        $dataType = static::TYPE_MAP[$field->getDataType()];
        if ('Single' != $field->getDataStructure()) {
            $dataType .= '[]';
        }

        return $dataType;
    }
}
