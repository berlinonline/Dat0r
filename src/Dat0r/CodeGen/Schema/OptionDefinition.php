<?php

namespace Dat0r\CodeGen\Schema;

use Dat0r;

class OptionDefinition extends Dat0r\Object
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
}
