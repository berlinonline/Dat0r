<?php

namespace Dat0r\Runtime\Attribute\ValueHolder;

use Dat0r\Common\Collection\TypedMap;
use Dat0r\Common\Collection\IUniqueCollection;

class ValueHolderMap extends TypedMap implements IUniqueCollection
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Attribute\\ValueHolder\\IValueHolder';
    }
}
