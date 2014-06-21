<?php

namespace Dat0r\CodeGen\ClassBuilder;

use Dat0r\Common\Object;
use Dat0r\CodeGen\Schema\TypeSchema;
use Dat0r\CodeGen\Schema\TypeDefinition;

use Dat0r\CodeGen\ClassBuilder\Common\BaseTypeClassBuilder as CommonBaseTypeClassBuilder;
use Dat0r\CodeGen\ClassBuilder\Common\TypeClassBuilder as CommonTypeClassBuilder;
use Dat0r\CodeGen\ClassBuilder\Common\BaseDocumentClassBuilder as CommonBaseDocumentClassBuilder;
use Dat0r\CodeGen\ClassBuilder\Common\DocumentClassBuilder as CommonDocumentClassBuilder;

use Dat0r\CodeGen\ClassBuilder\Aggregate\BaseTypeClassBuilder as AggregateBaseTypeClassBuilder;
use Dat0r\CodeGen\ClassBuilder\Aggregate\TypeClassBuilder as AggregateTypeClassBuilder;
use Dat0r\CodeGen\ClassBuilder\Aggregate\BaseDocumentClassBuilder as AggregateBaseDocumentClassBuilder;
use Dat0r\CodeGen\ClassBuilder\Aggregate\DocumentClassBuilder as AggregateDocumentClassBuilder;

use Dat0r\CodeGen\ClassBuilder\Reference\BaseTypeClassBuilder as ReferenceBaseTypeClassBuilder;
use Dat0r\CodeGen\ClassBuilder\Reference\TypeClassBuilder as ReferenceTypeClassBuilder;
use Dat0r\CodeGen\ClassBuilder\Reference\BaseDocumentClassBuilder as ReferenceBaseDocumentClassBuilder;
use Dat0r\CodeGen\ClassBuilder\Reference\DocumentClassBuilder as ReferenceDocumentClassBuilder;

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
            new CommonBaseTypeClassBuilder($builder_properties),
            new CommonTypeClassBuilder($builder_properties),
            new CommonBaseDocumentClassBuilder($builder_properties),
            new CommonDocumentClassBuilder($builder_properties)
        );
    }

    protected function createAggregateClassBuilders(TypeDefinition $aggregate)
    {
        $builder_properties = array(
            'type_schema' => $this->type_schema,
            'type_definition' => $aggregate
        );
        return array(
            new AggregateBaseTypeClassBuilder($builder_properties),
            new AggregateTypeClassBuilder($builder_properties),
            new AggregateBaseDocumentClassBuilder($builder_properties),
            new AggregateDocumentClassBuilder($builder_properties)
        );
    }

    protected function createReferenceClassBuilders(TypeDefinition $reference)
    {
        $builder_properties = array(
            'type_schema' => $this->type_schema,
            'type_definition' => $reference
        );
        return array(
            new ReferenceBaseTypeClassBuilder($builder_properties),
            new ReferenceTypeClassBuilder($builder_properties),
            new ReferenceBaseDocumentClassBuilder($builder_properties),
            new ReferenceDocumentClassBuilder($builder_properties)
        );
    }
}
