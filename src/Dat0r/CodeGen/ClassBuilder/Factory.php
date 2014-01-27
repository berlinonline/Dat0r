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
        $builder_properties = array(
            'module_schema' => $this->module_schema,
            'module_definition' => $module
        );
        return array(
            Common\BaseModuleClassBuilder::create($builder_properties),
            Common\ModuleClassBuilder::create($builder_properties),
            Common\BaseDocumentClassBuilder::create($builder_properties),
            Common\DocumentClassBuilder::create($builder_properties)
        );
    }

    protected function createAggregateClassBuilders(ModuleDefinition $aggregate)
    {
        $builder_properties = array(
            'module_schema' => $this->module_schema,
            'module_definition' => $aggregate
        );
        return array(
            Aggregate\BaseModuleClassBuilder::create($builder_properties),
            Aggregate\ModuleClassBuilder::create($builder_properties),
            Aggregate\BaseDocumentClassBuilder::create($builder_properties),
            Aggregate\DocumentClassBuilder::create($builder_properties)
        );
    }

    protected function createReferenceClassBuilders(ModuleDefinition $reference)
    {
        $builder_properties = array(
            'module_schema' => $this->module_schema,
            'module_definition' => $reference
        );
        return array(
            Reference\BaseModuleClassBuilder::create($builder_properties),
            Reference\ModuleClassBuilder::create($builder_properties),
            Reference\BaseDocumentClassBuilder::create($builder_properties),
            Reference\DocumentClassBuilder::create($builder_properties)
        );
    }
}
