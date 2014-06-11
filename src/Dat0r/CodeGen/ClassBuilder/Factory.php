<?php

namespace Dat0r\CodeGen\ClassBuilder;

use Dat0r\Common\Object;
use Dat0r\CodeGen\Schema\TypeSchema;
use Dat0r\CodeGen\Schema\TypeDefinition;

class Factory extends Object
{
    protected $type_schema;

    public function __construct(TypeSchema $schema = null)
    {
        $this->type_schema = $schema;
    }

    public function getTypeSchema()
    {
        return $this->type_schema;
    }

    public function setTypeSchema(TypeSchema $type_schema)
    {
        $this->type_schema = $type_schema;
    }

    public function createClassBuildersForType(TypeDefinition $type)
    {
        $class_builders = array();
        switch (get_class($type)) {
            case 'Dat0r\CodeGen\Schema\AggregateDefinition':
                $class_builders = $this->createAggregateClassBuilders($type);
                break;
            case 'Dat0r\CodeGen\Schema\ReferenceDefinition':
                $class_builders = $this->createReferenceClassBuilders($type);
                break;
            default:
                $class_builders = $this->createDefaultClassBuilders($type);
                break;
        }

        return $class_builders;
    }

    protected function createDefaultClassBuilders(TypeDefinition $type)
    {
        $builder_properties = array(
            'type_schema' => $this->type_schema,
            'type_definition' => $type
        );
        return array(
            Common\BaseTypeClassBuilder::create($builder_properties),
            Common\TypeClassBuilder::create($builder_properties),
            Common\BaseDocumentClassBuilder::create($builder_properties),
            Common\DocumentClassBuilder::create($builder_properties)
        );
    }

    protected function createAggregateClassBuilders(TypeDefinition $aggregate)
    {
        $builder_properties = array(
            'type_schema' => $this->type_schema,
            'type_definition' => $aggregate
        );
        return array(
            Aggregate\BaseTypeClassBuilder::create($builder_properties),
            Aggregate\TypeClassBuilder::create($builder_properties),
            Aggregate\BaseDocumentClassBuilder::create($builder_properties),
            Aggregate\DocumentClassBuilder::create($builder_properties)
        );
    }

    protected function createReferenceClassBuilders(TypeDefinition $reference)
    {
        $builder_properties = array(
            'type_schema' => $this->type_schema,
            'type_definition' => $reference
        );
        return array(
            Reference\BaseTypeClassBuilder::create($builder_properties),
            Reference\TypeClassBuilder::create($builder_properties),
            Reference\BaseDocumentClassBuilder::create($builder_properties),
            Reference\DocumentClassBuilder::create($builder_properties)
        );
    }
}
