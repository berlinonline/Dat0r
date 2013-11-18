<?php

namespace Dat0r\Runtime\Field;

use Dat0r\Common\Collection\TypedMap;
use Dat0r\Common\Collection\IUniqueCollection;

/**
 * FieldMap is a associative collection container, that maps fieldnames to correspondig field instances.
 * As fields must be unique by name, it is not recommended using this class outside of a module's scope.
 */
class FieldMap extends TypedMap implements IUniqueCollection
{
    /**
     * Returns the IField interface-name to the TypeMap parent-class,
     * which uses this info to implement it's type/instanceof strategy.
     *
     * @return string
     */
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Field\\IField';
    }
}
