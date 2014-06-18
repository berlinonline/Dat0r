<?php

namespace Dat0r\Runtime\Attribute\Value;

use Dat0r\Common\Collection\TypedMap;
use Dat0r\Common\Collection\IUniqueCollection;

class ValueMap extends TypedMap implements IUniqueCollection
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Attribute\\Value\\IValue';
    }
}
