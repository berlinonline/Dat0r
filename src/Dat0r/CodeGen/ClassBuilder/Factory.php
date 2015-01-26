<?php

namespace Dat0r\CodeGen\ClassBuilder;

use Dat0r\CodeGen\Config;
use Dat0r\Common\Object;
use Dat0r\CodeGen\Schema\TypeSchema;
use Dat0r\CodeGen\Schema\TypeDefinition;

use Dat0r\CodeGen\ClassBuilder\Common\BaseTypeClassBuilder as CommonBaseTypeClassBuilder;
use Dat0r\CodeGen\ClassBuilder\Common\TypeClassBuilder as CommonTypeClassBuilder;
use Dat0r\CodeGen\ClassBuilder\Common\BaseEntityClassBuilder as CommonBaseEntityClassBuilder;
use Dat0r\CodeGen\ClassBuilder\Common\EntityClassBuilder as CommonEntityClassBuilder;

use Dat0r\CodeGen\ClassBuilder\Aggregate\BaseTypeClassBuilder as AggregateBaseTypeClassBuilder;
use Dat0r\CodeGen\ClassBuilder\Aggregate\TypeClassBuilder as AggregateTypeClassBuilder;
use Dat0r\CodeGen\ClassBuilder\Aggregate\BaseEntityClassBuilder as AggregateBaseEntityClassBuilder;
use Dat0r\CodeGen\ClassBuilder\Aggregate\EntityClassBuilder as AggregateEntityClassBuilder;

use Dat0r\CodeGen\ClassBuilder\Reference\BaseTypeClassBuilder as ReferenceBaseTypeClassBuilder;
use Dat0r\CodeGen\ClassBuilder\Reference\TypeClassBuilder as ReferenceTypeClassBuilder;
use Dat0r\CodeGen\ClassBuilder\Reference\BaseEntityClassBuilder as ReferenceBaseEntityClassBuilder;
use Dat0r\CodeGen\ClassBuilder\Reference\EntityClassBuilder as ReferenceEntityClassBuilder;

class Factory extends Object
{
    protected $type_schema;

    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
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
            'type_definition' => $type,
            'config' => $this->config
        );
        return array(
            new CommonBaseTypeClassBuilder($builder_properties),
            new CommonTypeClassBuilder($builder_properties),
            new CommonBaseEntityClassBuilder($builder_properties),
            new CommonEntityClassBuilder($builder_properties)
        );
    }

    protected function createAggregateClassBuilders(TypeDefinition $aggregate)
    {
        $builder_properties = array(
            'type_schema' => $this->type_schema,
            'type_definition' => $aggregate,
            'config' => $this->config
        );
        return array(
            new AggregateBaseTypeClassBuilder($builder_properties),
            new AggregateTypeClassBuilder($builder_properties),
            new AggregateBaseEntityClassBuilder($builder_properties),
            new AggregateEntityClassBuilder($builder_properties)
        );
    }

    protected function createReferenceClassBuilders(TypeDefinition $reference)
    {
        $builder_properties = array(
            'type_schema' => $this->type_schema,
            'type_definition' => $reference,
            'config' => $this->config
        );
        return array(
            new ReferenceBaseTypeClassBuilder($builder_properties),
            new ReferenceTypeClassBuilder($builder_properties),
            new ReferenceBaseEntityClassBuilder($builder_properties),
            new ReferenceEntityClassBuilder($builder_properties)
        );
    }
}
