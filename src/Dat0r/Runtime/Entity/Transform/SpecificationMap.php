<?php

namespace Dat0r\Runtime\Entity\Transform;

use Dat0r\Common\Collection\TypedMap;
use Dat0r\Common\Collection\UniqueCollectionInterface;

class SpecificationMap extends TypedMap implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Entity\\Transform\\SpecificationInterface';
    }
}
