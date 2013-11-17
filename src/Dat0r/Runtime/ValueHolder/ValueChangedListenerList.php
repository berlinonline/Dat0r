<?php

namespace Dat0r\Runtime\ValueHolder;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\IUniqueCollection;

class ValueChangedListenerList extends TypedList implements IUniqueCollection
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\ValueHolder\\IValueChangedListener';
    }
}
