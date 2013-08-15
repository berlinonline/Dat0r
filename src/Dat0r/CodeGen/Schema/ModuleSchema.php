<?php

namespace Dat0r\CodeGen\Schema;

use Dat0r;

class ModuleSchema extends Dat0r\Object
{
    protected $namespace;

    protected $package;

    protected $module_definition;

    protected $aggregate_definitions;

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
        $aggregates = array();

        if (empty($names)) {
            return $this->aggregate_definitions;
        }

        foreach ($this->aggregate_definitions as $aggregate) {
            if (in_array($aggregate->getName(), $names)) {
                $aggregates[] = $aggregate;
            }
        }

        return $aggregates;
    }

    public function getUsedAggregateDefinitions(ModuleDefinition $module_definition)
    {
        $aggregates_set = ModuleDefinitionSet::create();
        $aggregate_fields = $module_definition->getFields()->filterByType('aggregate');

        foreach ($aggregate_fields as $aggregate_field) {
            $modules_options = $aggregate_field->getOptions()->filterByName('modules');
            $aggregates = $this->getAggregateDefinitions($modules_options->getValue()->toArray());
            foreach ($aggregates as $aggregate) {
                $aggregates_set->add($aggregate);
                foreach ($this->getUsedAggregateDefinitions($aggregate) as $nested_aggregate) {
                    $aggregates_set->add($nested_aggregate);
                }
            }
        }

        return $aggregates_set;
    }

    public function getPackage()
    {
        return $this->package;
    }

    protected function __construct()
    {
        $this->module_definition = ModuleDefinition::create();
        $this->aggregate_definitions = ModuleDefinitionSet::create();
    }
}
