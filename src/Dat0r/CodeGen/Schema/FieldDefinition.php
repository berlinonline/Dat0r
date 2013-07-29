<?php

namespace Dat0r\CodeGen\Schema;

use Dat0r;

class FieldDefinition extends Dat0r\Object
{
    protected $name;

    protected $implementor;

    protected $description;

    protected $short_name;

    protected $options;

    public function getName()
    {
        return $this->name;
    }

    public function getImplementor()
    {
        return $this->implementor;
    }

    public function getShortName()
    {
        return $this->short_name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getOptions()
    {
        return $this->options;
    }
}
