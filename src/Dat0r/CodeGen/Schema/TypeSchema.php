<?php

namespace Dat0r\CodeGen\Schema;

use Dat0r\Common\Object;

class TypeSchema extends Object
{
    protected $self_uri;

    protected $namespace;

    protected $package;

    protected $type_definition;

    protected $aggregate_definitions;

    protected $reference_definitions;

    public function __construct(array $state = array())
    {
        $this->type_definition = new TypeDefinition();
        $this->aggregate_definitions = new TypeDefinitionList();
        $this->reference_definitions = new TypeDefinitionList();

        parent::__construct($state);
    }

    public function getSelfUri()
    {
        return $this->self_uri;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getTypeDefinition()
    {
        return $this->type_definition;
    }

    public function setTypeDefinition(TypeDefinition $type_definition)
    {
        $this->type_definition = $type_definition;

        if (!$this->package) {
            $namespace_parts = explode('\\', $this->namespace);
            $this->package = end($namespace_parts);
        }
    }

    public function getAggregateDefinitions(array $names = array())
    {
        if (empty($names)) {
            return $this->aggregate_definitions;
        }

        $aggregates = array();
        foreach ($this->aggregate_definitions as $aggregate) {
            if (in_array($aggregate->getName(), $names)) {
                $aggregates[] = $aggregate;
            }
        }

        return $aggregates;
    }

    public function getUsedAggregateDefinitions(TypeDefinition $type_definition)
    {
        $aggregates_definitions_list = new TypeDefinitionList();
        $aggregate_attributes = $type_definition->getAttributes()->filterByType('aggregate-collection');

        foreach ($aggregate_attributes as $aggregate_attribute) {
            $aggregated_types_opt = $aggregate_attribute->getOptions()->filterByName('aggregates');
            $aggregates = $this->getAggregateDefinitions($aggregated_types_opt->getValue()->toArray());

            foreach ($aggregates as $aggregate) {
                $aggregates_definitions_list->addItem($aggregate);

                foreach ($this->getUsedAggregateDefinitions($aggregate) as $nested_aggregate) {
                    $aggregates_definitions_list->addItem($nested_aggregate);
                }
            }
        }

        return $aggregates_definitions_list;
    }

    public function getReferenceDefinitions(array $names = array())
    {
        if (empty($names)) {
            return $this->reference_definitions;
        }

        $references = array();
        foreach ($this->reference_definitions as $reference) {
            if (in_array($reference->getName(), $names)) {
                $references[] = $reference;
            }
        }

        return $references;
    }

    public function getUsedReferenceDefinitions(TypeDefinition $type_definition)
    {
        $reference_definitions_list = new TypeDefinitionList();
        $reference_attributes = $type_definition->getAttributes()->filterByType('reference-collection');

        foreach ($reference_attributes as $reference_attribute) {
            $references_option = $reference_attribute->getOptions()->filterByName('references');
            $references = $this->getReferenceDefinitions($references_option->getValue()->toArray());

            foreach ($references as $reference) {
                $reference_definitions_list->addItem($reference);
            }
        }

        foreach ($this->getUsedAggregateDefinitions($type_definition) as $aggregate) {
            foreach ($this->getUsedReferenceDefinitions($aggregate) as $reference) {
                $reference_definitions_list->addItem($reference);
            }
        }

        return $reference_definitions_list;
    }

    public function getPackage()
    {
        return $this->package;
    }
}
