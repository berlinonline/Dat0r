<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Object;

class FieldSpecificationSet extends Object implements IFieldSpecificationSet
{
    protected $name;

    protected $options;

    protected $field_specifications;

    public function getName()
    {
        return $this->name;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getFieldSpecifications()
    {
        return $this->field_specifications;
    }
}
