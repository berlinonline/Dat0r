<?php

namespace Dat0r\CodeGen;

use Dat0r;
use Dat0r\CodeGen\Parser;
use Dat0r\CodeGen\Schema;
use Dat0r\CodeGen\Builder;

class Service extends Dat0r\Object
{
    protected $config;

    protected $schema_parser;

    public function __construct()
    {
        $this->schema_parser = Parser\ModuleSchemaXmlParser::create();
    }

    public function buildSchema($module_schema_path)
    {
        $class_builders = $this->createClassBuilders(
            $this->schema_parser->parseSchema($module_schema_path)
        );

        $execute_build = function ($builder) {
            return $builder->build();
        };

        $class_list = Builder\ClassContainerList::create(
            array_map($execute_build, $class_builders)
        );

        $this->writeCache($class_list);
    }

    protected function createClassBuilders(Schema\ModuleSchema $module_schema)
    {
        $create_builders = function (Schema\ModuleDefinition $module) use ($module_schema) {
            return array(
                Builder\ModuleBaseClass::create($module_schema, $module),
                Builder\ModuleClass::create($module_schema, $module),
                Builder\DocumentBaseClass::create($module_schema, $module),
                Builder\DocumentClass::create($module_schema, $module)
            );
        };

        $root_module = $module_schema->getModuleDefinition();
        $class_builders = $create_builders($root_module);

        foreach ($root_module->getAggregateDefinitions($module_schema) as $aggregate_module) {
            $class_builders = array_merge($class_builders, $create_builders($aggregate_module));
        }

        return $class_builders;
    }

    protected function writeCache(Builder\ClassContainerList $class_list)
    {
        $cache_dir = realpath($this->config->getCachedir());

        if (!$cache_dir) {
            mkdir($this->config->getCachedir());
        }

        $cache_dir = realpath($this->config->getCachedir());

        if (!$cache_dir) {
            throw new Exception(
                sprintf(
                    "The configured cache directory %s is does not exist.",
                    $this->config->getCachedir()
                )
            );
        }

        foreach ($class_list as $class_container) {
            $rel_path = str_replace('\\', DIRECTORY_SEPARATOR, $class_container->getPackage());
            $package_dir = $cache_dir . DIRECTORY_SEPARATOR . $rel_path;

            if (!is_dir($package_dir)) {
                mkdir($package_dir, 0775, true);
            }

            $class_filepath = $package_dir . DIRECTORY_SEPARATOR . $class_container->getFileName();

            if (!file_put_contents($class_filepath, $class_container->getSourceCode())) {
                // @todo error handling ...
            }
        }
    }

    protected function moveCache()
    {
    }
}
