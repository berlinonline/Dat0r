<?php

namespace Dat0r\Core\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Dat0r\Core\CodeGenerator\Configuration;
use Dat0r\Core\CodeGenerator\ModuleDefinitionParser;
use Dat0r\Core\CodeGenerator\Builder;
use Dat0r\Core\CodeGenerator\Deployment;

class GenerateCodeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generate and/or deploy code for a given module definition.')
            ->addArgument(
                'config',
                InputArgument::REQUIRED,
                'Path pointing to a valid (ini) config file.'
            )
            ->addArgument(
                'definition',
                InputArgument::REQUIRED,
                'Path pointing to a valid (xml) module definition file.'
            )
            ->addArgument(
                'action',
                InputArgument::OPTIONAL,
                'Tell whether to generate and or deploy code. Valid values are `gen`, `dep` and `gen+dep`.',
                'gen+dep'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->validateInput($input);
        $this->processPayload($input, $output);
    }

    protected function validateInput(InputInterface $input)
    {
        $config = $input->getArgument('config');
        $definition = $input->getArgument('definition');
        $action = $input->getArgument('action');
        if (! is_readable(realpath($config)))
        {
            throw new Exception(
                sprintf('The given `config` argument path `%s` is not readable.', $config)
            );
        }
        if (! is_readable(realpath($definition)))
        {
            throw new Exception(
                sprintf('The given `definition` argument path `%s` is not readable.', $definition)
            );
        }
        $validActions = array('gen', 'dep', 'gen+dep');
        if (! in_array($action, $validActions))
        {
            throw new Exception(
                sprintf('The given `action` argument value `%s` is not supported.', $action)
            );
        }
    }

    protected function processPayload(InputInterface $input, OutputInterface $output)
    {
        try
        {
            // fetch and parse the module definition of interest
            $parser = ModuleDefinitionParser::create();
            $moduleDefinition = $parser->parse($input->getArgument('definition'));
            // then kick off code generation and/or deployment
            $actions = explode('+', $input->getArgument('action'));
            $configuration = $this->loadConfig($input);
            if (in_array('gen', $actions))
            {
                $builder = Builder::create($configuration);
                $builder->build($moduleDefinition);
            }
            if (in_array('dep', $actions))
            {
                $deployment = Deployment::create($configuration);
                $deployment->deploy($moduleDefinition);
            }
        }
        catch (\Exception $error)
        {
            throw new Exception("An error occured while trying to process command.\n-> " . $error->getMessage());
        }
    }

    protected function loadConfig(InputInterface $input)
    {
        $configPath = $input->getArgument('config');
        $basePath = dirname(realpath($configPath));

        $config = parse_ini_file($configPath);
        $cacheDir = $config['cacheDir'];
        $deployDir = $config['deployDir'];

        if (0 !== strpos($cacheDir, DIRECTORY_SEPARATOR))
        {
            $cacheDir = $this->normalizePath(
                $basePath . DIRECTORY_SEPARATOR . $cacheDir
            );
        }
        if (0 !== strpos($deployDir, DIRECTORY_SEPARATOR))
        {
            $deployDir = $this->normalizePath(
                $basePath . DIRECTORY_SEPARATOR . $deployDir
            );
        }
        
        $config['cacheDir'] = $cacheDir;
        $config['deployDir'] = $deployDir;

        return Configuration::create($config);
    }

    protected function normalizePath($path) 
    {
        return array_reduce(
            explode(DIRECTORY_SEPARATOR, $path), function($a, $b) {
                if(0 === $a)
                {
                    $a = DIRECTORY_SEPARATOR;
                }
                if("" === $b || "." === $b)
                {
                    return $a;
                }
                if(".." === $b)
                {
                    return dirname($a);
                }
                return preg_replace(
                    sprintf("/\%s+/", DIRECTORY_SEPARATOR), 
                    DIRECTORY_SEPARATOR, 
                    $a.DIRECTORY_SEPARATOR.$b
                );
            }, 0
        );
    }

    protected function displayUsage(OutputInterface $output)
    {
        $output->writeln($this->asText());
    }
}
