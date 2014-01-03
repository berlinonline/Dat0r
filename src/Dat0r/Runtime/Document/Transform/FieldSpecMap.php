<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Collection\TypedMap;
use Dat0r\Common\Collection\IUniqueCollection;

class FieldSpecMap extends TypedMap implements IUniqueCollection
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Document\\Transform\\IFieldSpec';
    }
}
