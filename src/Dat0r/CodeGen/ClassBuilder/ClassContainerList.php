<?php

namespace Dat0r\CodeGen\ClassBuilder;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\IUniqueCollection;

class ClassContainerList extends TypedList implements IUniqueCollection
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\CodeGen\\ClassBuilder\\ClassContainer';
    }
}
