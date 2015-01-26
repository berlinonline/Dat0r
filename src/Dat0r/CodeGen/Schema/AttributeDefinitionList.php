<?php

namespace Dat0r\CodeGen\Schema;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\UniqueCollectionInterface;

class AttributeDefinitionList extends TypedList implements UniqueCollectionInterface
{
    public function filterByType($type)
    {
        return $this->filter(
            function ($attribute) use ($type) {
                return $attribute->getShortName() === $type;
            }
        );
    }

    protected function getItemImplementor()
    {
        return '\\Dat0r\\CodeGen\\Schema\\AttributeDefinition';
    }
}
