<?php

namespace Dat0r\Common\Collection;

use Dat0r\Common\Error\RuntimeException;
use Closure;

class Map extends Collection implements IMap
{
    public function __construct(array $items = array())
    {
        parent::__construct();

        $this->setItems($items);
    }

    /**
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetUnset($offset)
    {
        if (isset($this->items[$offset])) {
            $removed_item = $this->items[$offset];
            unset($this->items[$offset]);
            $this->propagateCollectionChangedEvent(
                new CollectionChangedEvent($removed_item, CollectionChangedEvent::ITEM_REMOVED)
            );
        }
    }

    // ICollection

    public function filter(Closure $callback)
    {
        $filtered_map = new static();

        foreach ($this->items as $key => $item) {
            if ($callback($item) === true) {
                $filtered_map->setItem($key, $item);
            }
        }

        return $filtered_map;
    }

    // IMap

    public function setItem($key, $item)
    {
        $this->offsetSet($key, $item);
    }

    public function setItems(array $items)
    {
        foreach ($items as $key => $item) {
            $this->setItem($key, $item);
        }
    }

    public function getKeys()
    {
        return array_keys($this->items);
    }

    public function getValues()
    {
        return array_values($this->items);
    }

    public function append(ICollection $collection)
    {
        if (!$collection instanceof static) {
            throw new RuntimeException(
                sprintf("Can only append collections of the same type %s", get_class($this))
            );
        }

        foreach ($collection as $key => $item) {
            $this->setItem($key, $item);
        }
    }
}
