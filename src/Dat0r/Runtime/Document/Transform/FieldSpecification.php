<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Object;

class FieldSpecification extends Object implements IFieldSpecification
{
    protected $name;

    protected $options;

    public function getName()
    {
        return $this->name;
    }

    public function getOptions()
    {
        return $this->options;
    }
}
