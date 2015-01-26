<?php

namespace Dat0r\Runtime\Attribute\Value;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\UniqueCollectionInterface;

class ValueChangedListenerList extends TypedList implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Attribute\\Value\\IValueChangedListener';
    }
}
