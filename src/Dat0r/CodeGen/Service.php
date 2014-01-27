<?php

namespace Dat0r\CodeGen;

use Dat0r\Common\Object;
use Dat0r\CodeGen\Schema\ModuleSchema;
use Dat0r\CodeGen\ClassBuilder\Factory;
use Dat0r\CodeGen\ClassBuilder\ClassContainerList;
use Dat0r\Common\Error\FilesystemException;
use Symfony\Component\Filesystem\Filesystem;

class Service extends Object
{
    const DIR_MODE = 0750;

    const FILE_MODE = 0750;

    protected $config;

    protected $schema_parser;

    protected $class_builder_factory;

    protected $filesystem;

    protected $output;

    public function __construct()
    {
        $this->class_builder_factory = new Factory();
        $this->filesystem = new Filesystem();
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function buildSchema($module_schema_path)
    {
        $module_schema = $this->schema_parser->parse($module_schema_path);

        $class_container_list = new ClassContainerList();
        $class_container_list->addItems(
            array_map(
                function ($builder)
                {
                    return $builder->build();
                },
                $this->createClassBuilders($module_schema)
            )
        );

        $this->writeCache($class_container_list);
        $this->executePlugins($module_schema);
    }

    public function deployBuildCache()
    {
        $cache_dir = realpath($this->config->getCachedir());
        if (!is_dir($cache_dir)) {
            throw new FilesystemException(
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
            throw new FilesystemException(
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
        $this->class_builder_factory->setModuleSchema($module_schema);

        $root_module = $module_schema->getModuleDefinition();
        $class_builders = $this->class_builder_factory->createClassBuildersForModule($root_module);
        foreach ($module_schema->getUsedAggregateDefinitions($root_module) as $aggregate) {
            $aggregate_builders = $this->class_builder_factory->createClassBuildersForModule($aggregate);
            $class_builders = array_merge($class_builders, $aggregate_builders);
        }
        foreach ($module_schema->getUsedReferenceDefinitions($root_module) as $reference) {
            $reference_builders = $this->class_builder_factory->createClassBuildersForModule($reference);
            $class_builders = array_merge($class_builders, $reference_builders);
        }

        return $class_builders;
    }

    /**
     * @throws Symfony\Component\Filesystem\Exception\IOExceptionInterface
     */
    protected function writeCache(ClassContainerList $class_container_list)
    {
        $cache_dir = $this->config->getCachedir();
        if (!is_dir($cache_dir)) {
            $this->filesystem->mkdir($cache_dir, self::DIR_MODE);
        }

        foreach ($class_container_list as $class_container) {
            $relative_path = str_replace('\\', DIRECTORY_SEPARATOR, $class_container->getPackage());
            $package_dir = $cache_dir . DIRECTORY_SEPARATOR . $relative_path;

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
