<?php

namespace Dat0r\CodeGen;

use Dat0r;
use Dat0r\CodeGen\Config;
use Dat0r\CodeGen\Parser;
use Dat0r\CodeGen\Schema;
use Dat0r\CodeGen\Builder;

class Service extends Dat0r\Object
{
    protected $config;

    protected $schema_parser;

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
            mkdir($deploy_dir, 0752, true);
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
            $this->moveDirectory($cache_dir, $deploy_dir);
        } else {
            $this->copyDirectory($cache_dir, $deploy_dir);
        }
    }

    protected function copyDirectory($from, $to)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($from, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                 mkdir($to . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                copy($item, $to . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

    protected function moveDirectory($from, $to)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($from, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                mkdir($to . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                rename($item, $to . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
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
        $cache_dir = $this->config->getCachedir();
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0752, true);
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
                mkdir($package_dir, 0750, true);
            }

            $class_filepath = $package_dir . DIRECTORY_SEPARATOR . $class_container->getFileName();
            if (!file_put_contents($class_filepath, $class_container->getSourceCode())) {
                // @todo error handling ...
            }
        }
    }
}
