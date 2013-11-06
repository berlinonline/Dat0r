<?php

namespace Dat0r\CodeGen\Schema;

use Dat0r\TypedList;

class ModuleDefinitionList extends TypedList
{
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
        return '\\Dat0r\\CodeGen\\Schema\\ModuleDefinition';
    }
}
