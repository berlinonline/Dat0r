<?php

namespace Dat0r\CodeGen\Console;

use Symfony\Component\Console\Command;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output;
use Dat0r\CodeGen;
use Dat0r\CodeGen\Config;
use Dat0r\CodeGen\Parser;

class GenerateCodeCommand extends Command\Command
{
    const NAME = 'generate_code';

    protected static $generate_action_aliases = array('generate', 'gen', 'g');

    protected static $deploy_action_aliases = array('deploy', 'dep', 'd');

    protected function configure()
    {
        $this->setName(
            self::NAME
        )->setDescription(
            'Generate and/or deploy code for a given module schema_path.'
        )->addOption(
            'config',
            'c',
            Input\InputArgument::OPTIONAL,
            'Path pointing to a valid (ini) config file.'
        )->addOption(
            'schema',
            's',
            Input\InputArgument::OPTIONAL,
            'Path pointing to a valid (xml) module schema file.'
        )->addOption(
            'directory',
            'd',
            Input\InputArgument::OPTIONAL,
            'When the config or schema file are omitted, dat0r will look for standard files in this directory.'
        )->addArgument(
            'action',
            Input\InputArgument::OPTIONAL,
            'Tell whether to generate and or deploy code. Valid values are `gen`, `dep` and `gen+dep`.',
            'gen+dep'
        );
    }

    protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
    {
        $this->validateInput($input);
        $this->processPayload($input, $output);
    }

    protected function validateInput(Input\InputInterface $input)
    {
        $valid_actions = array_merge(
            self::$generate_action_aliases,
            self::$deploy_action_aliases
        );

        $config = $input->getOption('config');
        $schema_path = $input->getOption('schema');
        $actions = explode('+', $input->getArgument('action'));

        foreach ($actions as $action) {
            if (!in_array($action, $valid_actions)) {
                throw new Exception(
                    sprintf('The given `action` argument value `%s` is not supported.', $action)
                );
            }
        }
    }

    protected function processPayload(Input\InputInterface $input, Output\OutputInterface $output)
    {
        $actions = explode('+', $input->getArgument('action'));

        try {
            $module_schema = $this->getModuleSchemaPath($input);

            $service = CodeGen\Service::create(
                array(
                    'config' => $this->getConfig($input)->validate(),
                    'schema_parser' => Parser\ModuleSchemaXmlParser::create()
                )
            );

            $matched_actions = array_diff(self::$generate_action_aliases, $actions);
            if (count($matched_actions) < count(self::$generate_action_aliases)) {
                $service->buildSchema($module_schema);
            }

            $matched_actions = array_diff(self::$deploy_action_aliases, $actions);
            if (count($matched_actions) < count(self::$deploy_action_aliases)) {
                $service->deployBuild();
            }
        } catch (\Exception $error) {
            throw new Exception("An error occured while trying to process command.\n-> " . $error->getMessage());
        }
    }

    protected function getModuleSchemaPath(Input\InputInterface $input)
    {
        $schema_path = $input->getOption('schema');

        if (empty($schema_path)) {
            $schema_path = $this->getLookupDir($input) . DIRECTORY_SEPARATOR . 'dat0r.xml';
        }

        return $schema_path;
    }

    protected function getConfig(Input\InputInterface $input)
    {
        $config_path = $input->getOption('config');

        if (empty($config_path)) {
            $config_path = $this->getLookupDir($input) . DIRECTORY_SEPARATOR . 'dat0r.ini';
        }

        $config_reader = Config\IniFileConfigReader::create();
        $settings = $config_reader->read($config_path);

        return Config\Config::create($settings);
    }

    protected function getLookupDir(Input\InputInterface $input)
    {
        $lookup_dir = $input->getOption('directory');

        if (empty($lookup_dir)) {
            $lookup_dir = getcwd();
        }

        return $lookup_dir;
    }

    protected function displayUsage(Output\OutputInterface $output)
    {
        $output->writeln($this->asText());
    }
}
