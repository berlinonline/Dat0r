<?php

namespace Dat0r\Runtime\Document\Transform;

use Dat0r\Common\Collection\TypedMap;
use Dat0r\Common\Collection\IUniqueCollection;

class FieldSpecificationMap extends TypedMap implements IUniqueCollection
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Document\\Transform\\IFieldSpecification';
    }
}
