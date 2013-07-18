<?php

namespace Dat0r\CodeGen\Schema;

use Dat0r\Generic;

class ModuleSchema extends Generic\Object
{
    protected $namespace;

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

    public function getAggregateDefinitions()
    {
        return $this->aggregate_definitions;
    }

    protected function __construct()
    {
        $this->module_definition = ModuleDefinition::create();
        $this->aggregate_definitions = ModuleDefinitionSet::create();
    }
}
