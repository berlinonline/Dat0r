<?php

namespace Dat0r\CodeGen\ClassBuilder;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\UniqueCollectionInterface;

class ClassContainerList extends TypedList implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\CodeGen\\ClassBuilder\\ClassContainer';
    }
}
