<?php

namespace Dat0r\CodeGen\Schema;

class ModuleSchema
{
    private $namespace;

    private $module_definition;

    private $aggregate_definitions;

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

    public function toArray()
    {
        $data = array();

        foreach (get_object_vars($this) as $prop => $value)
        {
            if (is_object($value))
            {
                $data[$prop] = $value->toArray();
            }
            else
            {
                $data[$prop] = $value;
            }
        }

        return $data;
    }

    public static function create(array $data = array())
    {
        $module_schema = new static();

        foreach ($data as $key => $value)
        {
            if (property_exists($module_schema, $key))
            {
                $module_schema->$key = $value;
            }
        }

        return $module_schema;
    }

    protected function __construct()
    {
        $this->module_definition = ModuleDefinition::create();
        $this->aggregate_definitions = new ModuleDefinitionSet();
    }
}
