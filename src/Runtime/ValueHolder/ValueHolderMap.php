<?php

namespace Dat0r\Runtime\ValueHolder;

use Dat0r\Common\Collection\TypedMap;
use Dat0r\Common\Collection\UniqueCollectionInterface;

class ValueHolderMap extends TypedMap implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return ValueHolderInterface::CLASS;
    }
}
