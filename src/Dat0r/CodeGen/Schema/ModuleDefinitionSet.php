<?php

namespace Dat0r\CodeGen\Schema;

class ModuleDefinitionSet
{
    protected $module_definitions = array();

    public function __construct(array $module_definitions = array())
    {
        foreach ($module_definitions as $module_definition)
        {
            $this->add($module_definition);
        }
    }

    public function count()
    {
        return count($this->module_definitions);
    }

    public function offsetExists($offset)
    {
        return isset($this->module_definitions[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->module_definitions[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (!($value instanceof ModuleDefinition))
        {
            throw new \Exception("Invalid module-definition given.");
        }
        $this->module_definitions[$value->getName()] = $value;
    }

    public function offsetUnset($offset)
    {
        array_splice($this->module_definitions, $offset, 1);
    }

    public function current()
    {
        if ($this->valid())
        {
            return current($this->module_definitions);
        }
        else
        {
            return FALSE;
        }
    }

    public function key()
    {
        return key($this->module_definitions);
    }

    public function next()
    {
        return next($this->module_definitions);
    }

    public function rewind()
    {
        reset($this->module_definitions);
    }

    public function valid()
    {
        return NULL !== key($this->module_definitions);
    }

    public function first()
    {
        $fields = array_values($this->module_definitions);
        return 1 <= $this->count() ? $fields[0] : FALSE;
    }

    public function add(ModuleDefinition $module_definition)
    {
        $this->offsetSet($module_definition->getName(), $module_definition);
    }

    public function remove(ModuleDefinition $module_definition)
    {
        $this->offsetUnset($module_definition->getName());
    }

    public function toArray()
    {
        $data = array_map(function($module_definition)
        {
            return $module_definition->toArray();
        }, $this->module_definitions);

        return $data;
    }
}
