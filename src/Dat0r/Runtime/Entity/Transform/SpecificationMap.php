<?php

namespace Dat0r\Runtime\Entity\Transform;

use Dat0r\Common\Collection\TypedMap;
use Dat0r\Common\Collection\IUniqueCollection;

class SpecificationMap extends TypedMap implements IUniqueCollection
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Entity\\Transform\\ISpecification';
    }
}
