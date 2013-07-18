<?php

namespace Dat0r\CodeGen\Schema;

class FieldDefinitionSet
{
    protected $field_definitions = array();

    public function __construct(array $field_definitions = array())
    {
        foreach ($field_definitions as $field_definition)
        {
            $this->add($field_definition);
        }
    }

    public function count()
    {
        return count($this->field_definitions);
    }

    public function offsetExists($offset)
    {
        return isset($this->field_definitions[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->field_definitions[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (!($value instanceof FieldDefinition))
        {
            throw new \Exception("Invalid field definition given.");
        }
        $this->field_definitions[$value->getName()] = $value;
    }

    public function offsetUnset($offset)
    {
        array_splice($this->field_definitions, $offset, 1);
    }

    public function current()
    {
        if ($this->valid())
        {
            return current($this->field_definitions);
        }
        else
        {
            return FALSE;
        }
    }

    public function key()
    {
        return key($this->field_definitions);
    }

    public function next()
    {
        return next($this->field_definitions);
    }

    public function rewind()
    {
        reset($this->field_definitions);
    }

    public function valid()
    {
        return NULL !== key($this->field_definitions);
    }

    public function first()
    {
        $fields = array_values($this->field_definitions);
        return 1 <= $this->count() ? $fields[0] : FALSE;
    }

    public function add(FieldDefinition $field_definition)
    {
        $this->offsetSet($field_definition->getName(), $field_definition);
    }

    public function remove(FieldDefinition $field_definition)
    {
        $this->offsetUnset($field_definition->getName());
    }

    public function toArray()
    {
        $data = array_map(function($field_definition)
        {
            return $field_definition->toArray();
        }, $this->field_definitions);

        return $data;
    }
}
