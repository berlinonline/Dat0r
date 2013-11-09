<?php

namespace Dat0r\Runtime\Field;

use Dat0r\Common\Collection\TypedMap;
use Dat0r\Common\Collection\IUniqueCollection;

class FieldMap extends TypedMap implements IUniqueCollection
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Field\\IField';
    }
}
