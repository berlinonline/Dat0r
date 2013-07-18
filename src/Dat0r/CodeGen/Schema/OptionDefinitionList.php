<?php

namespace Dat0r\CodeGen\Schema;

class OptionDefinitionList
{
    private $option_definitions = array();

    public function __construct(array $option_definitions = array())
    {
        foreach ($option_definitions as $option_definition)
        {
            $this->add($option_definition);
        }
    }

    public function count()
    {
        return count($this->option_definitions);
    }

    public function offsetExists($offset)
    {
        return isset($this->option_definitions[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->option_definitions[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (!($value instanceof OptionDefinition))
        {
            throw new \Exception("Invalid option given.");
        }
        $this->option_definitions[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        array_splice($this->option_definitions, $offset, 1);
    }

    public function current()
    {
        if ($this->valid())
        {
            return current($this->option_definitions);
        }
        else
        {
            return FALSE;
        }
    }

    public function key()
    {
        return key($this->option_definitions);
    }

    public function next()
    {
        return next($this->option_definitions);
    }

    public function rewind()
    {
        reset($this->option_definitions);
    }

    public function valid()
    {
        return NULL !== key($this->option_definitions);
    }

    public function indexOf(OptionDefinition $option_definition)
    {
        return array_search($option_definition, $this->option_definitions, TRUE);
    }

    public function first()
    {
        return 1 <= $this->count() ? $this->option_definitions[0] : FALSE;
    }

    public function add(OptionDefinition $option_definition)
    {
        $this->option_definitions[] = $option_definition;
    }

    public function remove(OptionDefinition $option_definition)
    {
        $this->offsetUnset($this->indexOf($option_definition));
    }

    public function toArray()
    {
        $data = array();

        foreach ($this->option_definitions as $option)
        {
            $name = $option->getName();
            $value = $option->getValue();
            $next_value = $value;

            if ($value instanceof OptionDefinitionList)
            {
                $next_value = $value->toArray();
            }

            $next_value = $next_value ? $next_value : $option->getDefault();

            if ($name)
            {
                $data[$name] = $next_value;
            }
            else
            {
                $data[] = $next_value;
            }
        }

        return $data;
    }
}
