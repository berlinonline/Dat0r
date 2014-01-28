<?php

namespace Dat0r\CodeGen\Schema;

use Dat0r\Common\Object;

class ModuleSchema extends Object
{
    protected $self_uri;

    protected $namespace;

    protected $package;

    protected $module_definition;

    protected $aggregate_definitions;

    protected $reference_definitions;

    public function getSelfUri()
    {
        return $this->self_uri;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getModuleDefinition()
    {
        return $this->module_definition;
    }

    public function setModuleDefinition(ModuleDefinition $module_definition)
    {
        $this->module_definition = $module_definition;

        if (!$this->package) {
            $this->package = $module_definition->getName();
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

    public function getUsedAggregateDefinitions(ModuleDefinition $module_definition)
    {
        $aggregates_definitions_list = ModuleDefinitionList::create();
        $aggregate_fields = $module_definition->getFields()->filterByType('aggregate');
        foreach ($aggregate_fields as $aggregate_field) {
            $modules_options = $aggregate_field->getOptions()->filterByName('modules');
            $aggregates = $this->getAggregateDefinitions($modules_options->getValue()->toArray());
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

    public function getUsedReferenceDefinitions(ModuleDefinition $module_definition)
    {
        $reference_definitions_list = ModuleDefinitionList::create();
        $reference_fields = $module_definition->getFields()->filterByType('reference');
        foreach ($reference_fields as $reference_field) {
            $references_option = $reference_field->getOptions()->filterByName('references');
            $references = $this->getReferenceDefinitions($references_option->getValue()->toArray());
            foreach ($references as $reference) {
                $reference_definitions_list->addItem($reference);
            }
        }
        foreach ($this->getUsedAggregateDefinitions($module_definition) as $aggregate) {
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

    protected function __construct()
    {
        $this->module_definition = ModuleDefinition::create();
        $this->aggregate_definitions = ModuleDefinitionList::create();
        $this->reference_definitions = ModuleDefinitionList::create();
    }
}
