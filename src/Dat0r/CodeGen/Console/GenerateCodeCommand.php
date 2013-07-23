<?php

namespace Dat0r\CodeGen\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Dat0r\CodeGen;
use Dat0r\CodeGen\Parser;

class GenerateCodeCommand extends BaseCommand
{
    const NAME = 'generate_code';

    protected function configure()
    {
        $this->setName(
            self::NAME
        )->setDescription(
            'Generate and/or deploy code for a given module schema_path.'
        )->addOption(
            'config',
            'c',
            InputArgument::OPTIONAL,
            'Path pointing to a valid (ini) config file.'
        )->addOption(
            'schema',
            's',
            InputArgument::OPTIONAL,
            'Path pointing to a valid (xml) module schema file.'
        )->addOption(
            'directory',
            'd',
            InputArgument::OPTIONAL,
            'When the config or schema file are omitted, dat0r will look for standard files in this directory.'
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
        $config = $input->getOption('config');
        $schema_path = $input->getOption('schema');
        $actions = explode('+', $input->getArgument('action'));

        if (!is_readable(realpath($config))) {
            throw new Exception(
                sprintf('The given `config` argument path `%s` is not readable.', $config)
            );
        }

        if (!is_readable(realpath($schema_path))) {
            throw new Exception(
                sprintf('The given `schema_path` argument path `%s` is not readable.', $schema_path)
            );
        }

        $valid_actions = array('generate', 'gen', 'g', 'deploy', 'dep', 'd');
        foreach ($actions as $action) {
            if (!in_array($action, $valid_actions)) {
                throw new Exception(
                    sprintf('The given `action` argument value `%s` is not supported.', $action)
                );
            }
        }
    }

    protected function processPayload(InputInterface $input, OutputInterface $output)
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

            if (in_array(array('generate', 'gen', 'g'), $actions)) {
                $service->buildSchema($module_schema);
            }

            if (in_array(array('deploy', 'dep', 'd'), $actions)) {
                $service->deployBuild();
            }
        } catch (\Exception $error) {
            throw new Exception("An error occured while trying to process command.\n-> " . $error->getMessage());
        }
    }

    protected function getModuleSchemaPath(InputInterface $input)
    {
        $schema_path = $input->getOption('schema');

        if (empty($schema)) {
            $schema_path = $this->getLookupDir($input) . DIRECTORY_SEPARATOR . 'dat0r.xml';
        }

        return $schema_path;
    }

    protected function getConfig(InputInterface $input)
    {
        $config_path = $input->getOption('config');

        if (empty($config_path)) {
            $config_path = $this->getLookupDir($input) . DIRECTORY_SEPARATOR . 'dat0r.ini';
        }

        if (!is_readable($config_path)) {
            throw new Exception("Unable to read config file at: $config_path.");
        }

        return CodeGen\Config::create(
            $this->parseConfig($config_path)
        );
    }

    protected function parseConfig($config_path)
    {
        $settings = parse_ini_file($config_path, true);

        if ($settings === false) {
            throw new Exception("Unable to parse given config file: $config_path.");
        }

        if (isset($settings['cache_dir']) && $settings['cache_dir']{0} === '.') {
            $settings['cache_Dir'] = $this->resolvePathRelativeToBaseDir(
                $settings['cache_Dir'],
                dirname($config_path)
            );
        }

        if (isset($settings['deploy_dir']) && $settings['deploy_dir']{0} === '.') {
            $settings['deploy_dir'] = $this->resolvePathRelativeToBaseDir(
                $settings['deploy_dir'],
                dirname($config_path)
            );
        }

        return $settings;
    }

    protected function getLookupDir(InputInterface $input)
    {
        $lookup_dir = $input->getOption('directory');

        if (empty($lookup_dir)) {
            $lookup_dir = getcwd();
        }

        return $lookup_dir;
    }

    protected function displayUsage(OutputInterface $output)
    {
        $output->writeln($this->asText());
    }
}
