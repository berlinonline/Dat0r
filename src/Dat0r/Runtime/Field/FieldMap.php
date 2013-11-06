<?php

namespace Dat0r\Runtime\Field;

use Dat0r\Type\Collection\TypedMap;
use Dat0r\Type\Collection\IUniqueCollection;
use Dat0r\Runtime\Error\BadValueException;

class FieldMap extends TypedMap implements IUniqueCollection
{
    public function offsetSet($offset, $value)
    {
        if ($this->hasKey($offset)) {
            throw new BadValueException(
                "Field '". $offset ."' already exists in this collection. "
                . "Fieldnames are required to be unique per module."
            );
        }

        parent::offsetSet($offset, $value);
    }

    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Field\\IField';
    }
}
