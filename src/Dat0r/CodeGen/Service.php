<?php

namespace Dat0r\CodeGen;

use Dat0r\Common\Object;
use Dat0r\CodeGen\Config\IConfig;
use Dat0r\CodeGen\Schema\ModuleSchema;
use Dat0r\CodeGen\Schema\ModuleDefinition;
use Dat0r\CodeGen\Builder\ClassContainerList;
use Dat0r\CodeGen\Builder\ModuleBaseClass;
use Dat0r\CodeGen\Builder\ModuleClass;
use Dat0r\CodeGen\Builder\DocumentBaseClass;
use Dat0r\CodeGen\Builder\DocumentClass;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output;

class Service extends Object
{
    const DIR_MODE = 0750;

    const FILE_MODE = 0750;

    protected $config;

    protected $schema_parser;

    protected $filesystem;

    protected $output;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    public function setConfig(IConfig $config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function buildSchema($module_schema_path)
    {
        $module_schema = $this->schema_parser->parseSchema($module_schema_path);
        $class_builders = $this->createClassBuilders($module_schema);

        $execute_build = function ($builder) {
            return $builder->build();
        };

        $class_list = ClassContainerList::create(
            array_map($execute_build, $class_builders)
        );

        $this->writeCache($class_list);
        $this->executePlugins($module_schema);
    }

    public function deployBuild()
    {
        $cache_dir = realpath($this->config->getCachedir());
        if (!is_dir($cache_dir)) {
            throw new Exception(
                sprintf(
                    "The cache directory '%s' to deploy from does not exist.",
                    $this->config->getCachedir()
                )
            );
        }

        $deploy_dir = $this->config->getDeployDir();
        if (!is_dir($deploy_dir)) {
            $this->writeMessage('<info>Creating directory: ' . $deploy_dir . ' ...</info>');
            $this->filesystem->mkdir($deploy_dir, self::DIR_MODE);
        }

        if (!($deploy_dir = realpath($deploy_dir))) {
            throw new Exception(
                sprintf(
                    "The configured deploy directory %s does not exist and could not be created.",
                    $this->config->getDeployDir()
                )
            );
        }

        $method = $this->config->getDeployMethod();
        if ($method === 'move') {
            $this->writeMessage('<info>Moving generated files to directory: ' . $deploy_dir . ' ...</info>');
            $this->filesystem->rename($cache_dir, $deploy_dir, true);
        } else {
            $this->writeMessage('<info>Copying generated files to directory: ' . $deploy_dir . ' ...</info>');
            $this->filesystem->mirror($cache_dir, $deploy_dir, null, array('override' => true));
        }
    }

    protected function createClassBuilders(ModuleSchema $module_schema)
    {
        $create_builders = function (ModuleDefinition $module) use ($module_schema) {
            return array(
                ModuleBaseClass::create($module_schema, $module),
                ModuleClass::create($module_schema, $module),
                DocumentBaseClass::create($module_schema, $module),
                DocumentClass::create($module_schema, $module)
            );
        };

        $root_module = $module_schema->getModuleDefinition();
        $class_builders = $create_builders($root_module);

        foreach ($module_schema->getUsedAggregateDefinitions($root_module) as $aggregate_module) {
            $class_builders = array_merge($class_builders, $create_builders($aggregate_module));
        }

        return $class_builders;
    }

    protected function writeCache(ClassContainerList $class_list)
    {
        $cache_dir = $this->config->getCachedir();
        if (!is_dir($cache_dir)) {
            $this->filesystem->mkdir($cache_dir, self::DIR_MODE);
        }

        if (!($cache_dir = realpath($cache_dir))) {
            throw new Exception(
                sprintf(
                    "The configured cache directory %s does not exist and could not be created.",
                    $this->config->getCachedir()
                )
            );
        }

        foreach ($class_list as $class_container) {
            $rel_path = str_replace('\\', DIRECTORY_SEPARATOR, $class_container->getPackage());
            $package_dir = $cache_dir . DIRECTORY_SEPARATOR . $rel_path;

            if (!is_dir($package_dir)) {
                $this->filesystem->mkdir($package_dir, self::DIR_MODE);
            }

            $class_filepath = $package_dir . DIRECTORY_SEPARATOR . $class_container->getFileName();

            $this->filesystem->dumpFile(
                $class_filepath,
                $class_container->getSourceCode(),
                self::FILE_MODE
            );
        }
    }

    protected function executePlugins(ModuleSchema $module_schema)
    {
        foreach ($this->config->getPluginSettings() as $plugin_settings) {
            $plugin_class = $plugin_settings['implementor'];
            require_once $plugin_settings['path'];
            $plugin = new $plugin_class($plugin_settings['options']);
            $plugin->execute($module_schema);
        }
    }

    protected function writeMessage($message)
    {
        if ($this->output) {
            $this->output->writeln($message);
        }
    }
}
