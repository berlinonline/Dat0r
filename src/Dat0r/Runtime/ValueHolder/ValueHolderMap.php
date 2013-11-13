<?php

namespace Dat0r\Runtime\ValueHolder;

use Dat0r\Common\Collection\TypedMap;
use Dat0r\Common\Collection\IUniqueCollection;

class ValueHolderMap extends TypedMap implements IUniqueCollection
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\ValueHolder\\IValueHolder';
    }
}
