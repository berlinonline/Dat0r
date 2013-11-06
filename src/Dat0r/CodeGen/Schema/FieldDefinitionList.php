<?php

namespace Dat0r\CodeGen\Schema;

use Dat0r\TypedList;

class FieldDefinitionList extends TypedList
{
    public function filterByType($type)
    {
        return $this->filter(
            function ($field) use ($type) {
                return $field->getShortName() === $type;
            }
        );
    }

    public function offsetSet($offset, $value)
    {
        foreach ($this->items as $index => $item) {
            if ($item === $value) {
                $offset = $index;
                break;
            }
        }

        parent::offsetSet($offset, $value);
    }

    protected function getItemImplementor()
    {
        return '\\Dat0r\\CodeGen\\Schema\\FieldDefinition';
    }
}
