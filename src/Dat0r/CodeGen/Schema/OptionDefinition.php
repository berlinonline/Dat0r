<?php

namespace Dat0r\CodeGen\Schema;

use Dat0r\Generic;

class OptionDefinition extends Generic\Object
{
    protected $name;

    protected $value;

    protected $default;

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
}
