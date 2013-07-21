<?php

namespace Dat0r\CodeGen;

use Dat0r\CodeGen\Parser;
use Dat0r\CodeGen\Schema;
use Dat0r\CodeGen\Builder;

class Service
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function buildSchema($module_schema_path)
    {
        $schema_parser = Parser\ModuleSchemaXmlParser::create();
        $module_schema = $schema_parser->parseSchema($module_schema_path);

        $module_definition = $module_schema->getModuleDefinition();
        $module_name = $module_definition->getName();

        $builders = array(
            Builder\ModuleBaseClass::create(),
            Builder\ModuleClass::create(),
            Builder\DocumentBaseClass::create(),
            Builder\DocumentClass::create()
        );

        $build_code = function($builder) use ($module_schema) {
            return $builder->build($module_schema);
        };

        $class_list = Builder\ClassContainerList::create(
            array_map($build_code, $builders)
        );

        $this->writeCache($class_list);
    }

    protected function writeCache(Builder\ClassContainerList $class_list)
    {
        $cache_dir = realpath($this->config->getCachedir());

        if (!$cache_dir)
        {
            mkdir($this->config->getCachedir());
        }

        $cache_dir = realpath($this->config->getCachedir());

        if (!$cache_dir)
        {
            throw new Exception(
                sprintf(
                    "The configured cache directory %s is does not exist.",
                    $this->config->getCachedir()
                )
            );
        }

        foreach ($class_list as $class_container)
        {

var_dump($class_container->toArray());

        }
    }

    protected function moveCache()
    {

    }
}
