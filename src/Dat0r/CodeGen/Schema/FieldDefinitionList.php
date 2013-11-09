<?php

namespace Dat0r\CodeGen\Schema;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\IUniqueCollection;

class FieldDefinitionList extends TypedList implements IUniqueCollection
{
    public function filterByType($type)
    {
        return $this->filter(
            function ($field) use ($type) {
                return $field->getShortName() === $type;
            }
        );
    }

    protected function getItemImplementor()
    {
        return '\\Dat0r\\CodeGen\\Schema\\FieldDefinition';
    }
}
