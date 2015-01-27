<?php

namespace Dat0r\Runtime\ValueHolder;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\UniqueCollectionInterface;

/**
 * Represents a list of value-changed events.
 */
class ValueChangedEventList extends TypedList implements UniqueCollectionInterface
{
    /**
     * Returns the ValueChangedEvent class-name to the TypeList parent-class,
     * which uses this info to implement it's type/instanceof strategy.
     *
     * @return string
     */
    protected function getItemImplementor()
    {
        return ValueChangedEvent::CLASS;
    }
}
