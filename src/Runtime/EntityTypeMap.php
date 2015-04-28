<?php

namespace Dat0r\Runtime;

use Dat0r\Common\Collection\TypedMap;
use Dat0r\Common\Collection\UniqueCollectionInterface;
use Dat0r\Runtime\EntityTypeInterface;

class EntityTypeMap extends TypedMap implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return EntityTypeInterface::CLASS;
    }
}
