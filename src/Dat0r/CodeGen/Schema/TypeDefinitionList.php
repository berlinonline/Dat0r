<?php

namespace Dat0r\CodeGen\Schema;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\UniqueCollectionInterface;

class TypeDefinitionList extends TypedList implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\CodeGen\\Schema\\TypeDefinition';
    }
}
