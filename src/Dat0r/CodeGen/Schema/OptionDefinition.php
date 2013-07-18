<?php

namespace Dat0r\CodeGen\Schema;

class OptionDefinition
{
    private $name;

    private $value;

    private $default;

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function toArray()
    {
        $data = array();

        if ($this->value instanceof OptionDefinitionList)
        {
            $data[$this->name] = array();

            foreach ($this->value as $option)
            {
                $data = array_merge($data[$this->name], $option->toArray());
            }
        }
        else if ($this->name)
        {
            $data[$this->name] = $this->value;
        }
        else
        {
            $data = $value;
        }

        return $data;
    }

    public static function create(array $data = array())
    {
        $option_definition = new static();

        foreach ($data as $key => $value)
        {
            if (property_exists($option_definition, $key))
            {
                $option_definition->$key = $value;
            }
        }

        return $option_definition;
    }

    protected function __construct() {}
}
