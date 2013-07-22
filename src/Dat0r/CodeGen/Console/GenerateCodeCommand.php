<?php

namespace Dat0r\CodeGen\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Dat0r\CodeGen;

class GenerateCodeCommand extends BaseCommand
{
    const NAME = 'generate_code';

    protected function configure()
    {
        $this->setName(
            self::NAME
        )->setDescription(
            'Generate and/or deploy code for a given module schema_path.'
        )->addArgument(
            'config',
            InputArgument::REQUIRED,
            'Path pointing to a valid (ini) config file.'
        )->addArgument(
            'schema_path',
            InputArgument::REQUIRED,
            'Path pointing to a valid (xml) module schema file.'
        )->addArgument(
            'action',
            InputArgument::OPTIONAL,
            'Tell whether to generate and or deploy code. Valid values are `gen`, `dep` and `gen+dep`.',
            'gen+dep'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->validateInput($input);
        $this->processPayload($input, $output);
    }

    protected function validateInput(InputInterface $input)
    {
        $config = $input->getArgument('config');
        $schema_path = $input->getArgument('schema_path');
        $action = $input->getArgument('action');

        if (! is_readable(realpath($config))) {
            throw new Exception(
                sprintf('The given `config` argument path `%s` is not readable.', $config)
            );
        }

        if (! is_readable(realpath($schema_path))) {
            throw new Exception(
                sprintf('The given `schema_path` argument path `%s` is not readable.', $schema_path)
            );
        }

        $validActions = array('gen', 'dep', 'gen+dep');
        if (! in_array($action, $validActions)) {
            throw new Exception(
                sprintf('The given `action` argument value `%s` is not supported.', $action)
            );
        }
    }

    protected function processPayload(InputInterface $input, OutputInterface $output)
    {
        try {
            $service = CodeGen\Service::create($this->loadConfig());

            if (in_array('gen', $actions)) {
                $service->buildSchema($input->getArgument('schema_path'));
            }

            if (in_array('dep', $actions)) {
                $service->deployBuild();
            }
        } catch (\Exception $error) {
            throw new Exception("An error occured while trying to process command.\n-> " . $error->getMessage());
        }
    }

    protected function loadConfig(InputInterface $input)
    {
        $configPath = $input->getArgument('config');
        $config = parse_ini_file($configPath, true);

        return CodeGen\Config::create($config);
    }

    protected function displayUsage(OutputInterface $output)
    {
        $output->writeln($this->asText());
    }
}
