<?php

namespace Dat0r\CodeGen\Builder;

use Dat0r\Type\Collection\TypedList;
use Dat0r\Type\Collection\IUniqueCollection;

class ClassContainerList extends TypedList implements IUniqueCollection
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\CodeGen\\Builder\\ClassContainer';
    }
}
