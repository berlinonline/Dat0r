<?php

namespace Dat0r\CodeGen\ClassBuilder;

use Dat0r\Common\Object;
use Dat0r\CodeGen\Config\IConfig;
use Dat0r\CodeGen\Schema\ModuleSchema;
use Dat0r\CodeGen\Schema\ModuleDefinition;

class Factory extends Object
{
    protected $module_schema;

    public function __construct(ModuleSchema $schema = null)
    {
        $this->module_schema = $schema;
    }

    public function getModuleSchema()
    {
        return $this->module_schema;
    }

    public function setModuleSchema(ModuleSchema $module_schema)
    {
        $this->module_schema = $module_schema;
    }

    public function createClassBuildersForModule(ModuleDefinition $module)
    {
        $class_builders = array();
        switch (get_class($module)) {
            case 'Dat0r\CodeGen\Schema\AggregateDefinition':
                $class_builders = $this->createAggregateClassBuilders($module);
                break;
            case 'Dat0r\CodeGen\Schema\ReferenceDefinition':
                $class_builders = $this->createReferenceClassBuilders($module);
                break;
            default:
                $class_builders = $this->createDefaultClassBuilders($module);
                break;
        }

        return $class_builders;
    }

    protected function createDefaultClassBuilders(ModuleDefinition $module)
    {
        $class_builder_params = array(
            'module_schema' => $this->module_schema,
            'module_definition' => $module
        );
        return array(
            Common\BaseModuleClassBuilder::create($class_builder_params),
            Common\ModuleClassBuilder::create($class_builder_params),
            Common\BaseDocumentClassBuilder::create($class_builder_params),
            Common\DocumentClassBuilder::create($class_builder_params)
        );
    }

    protected function createAggregateClassBuilders(ModuleDefinition $aggregate)
    {
        $class_builder_params = array(
            'module_schema' => $this->module_schema,
            'module_definition' => $aggregate
        );
        return array(
            Aggregate\BaseModuleClassBuilder::create($class_builder_params),
            Aggregate\ModuleClassBuilder::create($class_builder_params),
            Aggregate\BaseDocumentClassBuilder::create($class_builder_params),
            Aggregate\DocumentClassBuilder::create($class_builder_params)
        );
    }

    protected function createReferenceClassBuilders(ModuleDefinition $reference)
    {
        $class_builder_params = array(
            'module_schema' => $this->module_schema,
            'module_definition' => $reference
        );
        return array(
            Reference\BaseModuleClassBuilder::create($class_builder_params),
            Reference\ModuleClassBuilder::create($class_builder_params),
            Reference\BaseDocumentClassBuilder::create($class_builder_params),
            Reference\DocumentClassBuilder::create($class_builder_params)
        );
    }
}
