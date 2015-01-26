<?php

namespace Dat0r\Runtime\Attribute\Value;

use Dat0r\Common\Collection\TypedMap;
use Dat0r\Common\Collection\UniqueCollectionInterface;

class ValueMap extends TypedMap implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Attribute\\Value\\ValueInterface';
    }
}
