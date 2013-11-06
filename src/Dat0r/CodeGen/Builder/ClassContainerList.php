<?php

namespace Dat0r\CodeGen\Builder;

use Dat0r\TypedList;

class ClassContainerList extends TypedList
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\CodeGen\\Builder\\ClassContainer';
    }
}
