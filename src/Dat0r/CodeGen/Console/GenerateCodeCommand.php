<?php

namespace Dat0r\CodeGen\Console;

use Dat0r\CodeGen\Service;
use Dat0r\CodeGen\Parser\Config\ConfigIniParser;
use Dat0r\CodeGen\Parser\ModuleSchema\ModuleSchemaXmlParser;
use Dat0r\Common\Error\BadValueException;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCodeCommand extends Command
{
    protected static $generate_action_aliases = array('generate', 'gen', 'g');

    protected static $deploy_action_aliases = array('deploy', 'dep', 'd');

    protected $service;

    public function setService(Service $service)
    {
        $this->service = $service;
    }

    protected function configure()
    {
        $this->setName('generate_code')
            ->setDescription('Generate and/or deploy code for a given module schema_path.')
            ->addOption('config', 'c', InputArgument::OPTIONAL, 'Path pointing to a valid (ini) config file.')
            ->addOption('schema', 's', InputArgument::OPTIONAL, 'Path pointing to a valid (xml) module schema file.')
            ->addOption('directory', 'd', InputArgument::OPTIONAL, 'When the config or schema file are omitted, dat0r will look for standard files in this directory.')
            ->addArgument('action', InputArgument::OPTIONAL, 'Tell whether to generate and or deploy code. Valid values are `gen`, `dep` and `gen+dep`.', 'gen+dep');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $input_actions = $this->validateInputAction($input);
        $service = $this->fetchConfiguredService($input, $output);

        if (in_array('generate', $input_actions)) {
            $service->buildSchema($this->getModuleSchemaPath($input));
        }
        if (in_array('deploy', $input_actions)) {
            $service->deployBuildCache();
        }
    }

    protected function validateInputAction(InputInterface $input)
    {
        $valid_actions = array_merge(self::$generate_action_aliases, self::$deploy_action_aliases);
        $input_actions = explode('+', $input->getArgument('action'));
        foreach ($input_actions as $input_action) {
            if (!in_array($input_action, $valid_actions)) {
                throw new BadValueException(
                    sprintf('The given `action` argument value `%s` is not supported.', $input_action)
                );
            }
        }

        $diff_count = count(array_diff(self::$generate_action_aliases, $input_actions));
        if ($diff_count < count(self::$generate_action_aliases)) {
            $sanitized_actions[] = 'generate';
        }
        $diff_count = count(array_diff(self::$deploy_action_aliases, $input_actions));
        if ($diff_count < count(self::$deploy_action_aliases)) {
            $sanitized_actions[] = 'deploy';
        }

        return $sanitized_actions;
    }

    protected function fetchConfiguredService(InputInterface $input, OutputInterface $output)
    {
        if (!$this->service) {
            $this->service = Service::create(
                array(
                    'config' => $this->createConfig($input)->validate(),
                    'schema_parser' => ModuleSchemaXmlParser::create(),
                    'output' => $output
                )
            );
        }

        return $this->service;
    }

    protected function createConfig(InputInterface $input)
    {
        $config_path = $input->getOption('config');
        if (empty($config_path)) {
            $config_path = $this->getLookupDir($input) . DIRECTORY_SEPARATOR . 'dat0r.ini';
        }
        $this->service_config = ConfigIniParser::create()->parse($config_path);

        return $this->service_config;
    }

    protected function getLookupDir(InputInterface $input)
    {
        $lookup_dir = $input->getOption('directory');
        if (empty($lookup_dir)) {
            $lookup_dir = getcwd();
        }

        return $lookup_dir;
    }

    protected function getModuleSchemaPath(InputInterface $input)
    {
        $schema_path = $input->getOption('schema');
        if (empty($schema_path)) {
            $schema_path = $this->getLookupDir($input) . DIRECTORY_SEPARATOR . 'dat0r.xml';
        }

        return $schema_path;
    }

    protected function displayUsage(OutputInterface $output)
    {
        $output->writeln($this->asText());
    }
}
