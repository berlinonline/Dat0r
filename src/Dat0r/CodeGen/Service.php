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

    protected $output;

    public function __construct()
    {
        $this->class_builder_factory = new Factory();
        // @todo use this as an adpater for message output
        $this->output_handler = function() {
            // noop default output handler
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

        $build_cache = BuildCache::create(
            array('cache_directory' => $this->config->getCachedir())
        );
        $build_cache->generate($class_container_list);

        $this->executePlugins($module_schema);
    }

    public function deploy()
    {
        $build_cache = BuildCache::create(
            array('cache_directory' => $this->config->getCachedir())
        );

        $build_cache->deploy(
            $this->config->getDeployDir(),
            $this->config->getDeployMethod()
        );
    }

    public function getConfig()
    {
        return $this->config;
    }

    protected function setConfig(Config $config)
    {
        $this->config = $config;

        if ($bootstrap_file = $this->config->getBootstrapFile()) {
            require_once $bootstrap_file;
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

    protected function executePlugins(ModuleSchema $module_schema)
    {
        foreach ($this->config->getPluginSettings() as $plugin_settings) {
            $plugin_class = $plugin_settings['implementor'];
            if (class_exists($plugin_class)) {
                if (is_a('\\Dat0r\\CodeGen\\IPlugin', $plugin_class)) {
                    $plugin = new $plugin_class($plugin_settings['options']);
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
        if ($this->output) {
            $this->output->writeln($message);
        }
    }
}
