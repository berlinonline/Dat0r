<?php

namespace Dat0r\CodeGen;

use Dat0r\Common\Object;
use Dat0r\CodeGen\Schema\ModuleSchema;
use Dat0r\CodeGen\ClassBuilder\Factory;
use Dat0r\CodeGen\ClassBuilder\ClassContainerList;
use Dat0r\CodeGen\ClassBuilder\BuildCache;

class Service extends Object
{
    protected $config;

    protected $schema_parser;

    protected $class_builder_factory;

    protected $build_cache;

    protected $output_handler;

    public function __construct()
    {
        $this->class_builder_factory = new Factory();
        $this->output_handler = function ($message) {
            echo $message . PHP_EOL;
        };
    }

    public function generate($module_schema_path)
    {
        $module_schema = $this->schema_parser->parse($module_schema_path);

        $class_container_list = new ClassContainerList();
        $class_container_list->addItems(
            array_map(
                function ($builder) {
                    return $builder->build();
                },
                $this->createClassBuilders($module_schema)
            )
        );

        $this->build_cache->generate($class_container_list);
        $this->executePlugins($module_schema);
    }

    public function deploy($module_schema_path)
    {
        $module_schema = $this->schema_parser->parse($module_schema_path);

        $class_container_list = new ClassContainerList();
        $class_container_list->addItems(
            array_map(
                function ($builder) {
                    return $builder->build();
                },
                $this->createClassBuilders($module_schema)
            )
        );

        $this->build_cache->deploy($class_container_list, $this->config->getDeployMethod());
    }

    public function getConfig()
    {
        return $this->config;
    }

    protected function setConfig(Config $config)
    {
        $this->config = $config;

        $this->build_cache = BuildCache::create(
            array(
                'cache_directory' => $this->config->getCacheDir(),
                'deploy_directory' => $this->config->getDeployDir()
            )
        );

        $bootstrap = function ($bootstrap_file = null) {
            if ($bootstrap_file) {
                require_once $bootstrap_file;
            }
        };

        $bootstrap($this->config->getBootstrapFile());
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

    protected function executePlugins(ModuleSchema $module_schema)
    {
        foreach ($this->config->getPluginSettings() as $plugin_class => $plugin_options) {
            if (class_exists($plugin_class)) {
                if (is_a($plugin_class, '\\Dat0r\\CodeGen\\IPlugin', true)) {
                    $plugin = new $plugin_class($plugin_options);
                    $plugin->execute($module_schema);
                } else {
                    $warning = '<warning>Plugin class: `%s`, does not implement the IPlugin interface.</warning>';
                    $this->writeMessage(sprintf($warning, $plugin_class));
                }
            } else {
                $warning = '<warning>Unable to load plugin class: `%s`</warning>';
                $this->writeMessage(sprintf($warning, $plugin_class));
            }
        }
    }

    protected function writeMessage($message)
    {
        $write_message = $this->output_handler;
        $write_message($message);
    }
}
