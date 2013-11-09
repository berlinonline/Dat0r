<?php

namespace Dat0r\CodeGen\Schema;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\IUniqueCollection;

class ModuleDefinitionList extends TypedList implements IUniqueCollection
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\CodeGen\\Schema\\ModuleDefinition';
    }
}
