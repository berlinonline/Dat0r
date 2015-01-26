<?php

namespace Dat0r\Runtime\Entity;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\UniqueCollectionInterface;

/**
 * Represents a list of entity-changed listeners.
 */
class EntityChangedListenerList extends TypedList implements UniqueCollectionInterface
{
    /**
     * Returns the IEntityChangedListener interface-name to the TypeList parent-class,
     * which uses this info to implement it's type/instanceof strategy.
     *
     * @return string
     */
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Entity\\IEntityChangedListener';
    }
}
