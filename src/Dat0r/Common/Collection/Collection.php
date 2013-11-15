<?php

namespace Dat0r\Common\Collection;

use Dat0r\Common\Object;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Common\Error\BadValueException;

abstract class Collection extends Object implements ICollection
{
    protected $items;

    protected $collection_listeners;

    public function __construct()
    {
        $this->items = array();
        $this->collection_listeners = array();
    }

    // PHP Interface - Countable

    /**
     * @see http://php.net/manual/en/class.countable.php
     */
    public function count()
    {
        return count($this->items);
    }

    // PHP Interface - ArrayAccess

    /**
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetGet($offset)
    {
        if (isset($this->items[$offset])) {
            return $this->items[$offset];
        }
        return null;
    }

    /**
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetSet($offset, $value)
    {
        if ($this instanceof IUniqueCollection) {
            if (false !== ($item_key = array_search($value, $this->items, true))) {
                throw new RuntimeException("Item allready has been added to the collection at key: " . $item_key);
            }
        }
        $this->items[$offset] = $value;
        $this->propagateCollectionChangedEvent(
            new CollectionChangedEvent($value, CollectionChangedEvent::ITEM_ADDED)
        );
    }

    /**
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetUnset($offset)
    {
        $removed_items = array_splice($this->items, $offset, 1);
        $this->propagateCollectionChangedEvent(
            new CollectionChangedEvent($removed_items[0], CollectionChangedEvent::ITEM_REMOVED)
        );
    }

    /**
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function key()
    {
        return key($this->items);
    }

    // PHP Interface - Iterator

    /**
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function valid()
    {
        return null !== $this->key();
    }

    /**
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function current()
    {
        if ($this->valid()) {
            return current($this->items);
        } else {
            return false;
        }
    }

    /**
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function next()
    {
        return next($this->items);
    }

    /**
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function rewind()
    {
        return reset($this->items);
    }

    // Dat0r Interface - ICollection

    public function getItem($key)
    {
        return $this->offsetGet($key);
    }

    public function getItems(array $keys)
    {
        $items = array();
        foreach ($keys as $key) {
            $items[] = $this->offsetGet($key);
        }

        return $items;
    }

    public function setItem($key, $item)
    {
        if ($key === null) {
            throw new BadValueException('Key may not be null.');
        }
        $this->offsetSet($key, $item);
    }

    public function removeItem($item)
    {
        $this->offsetUnset($this->getKey($item));
    }

    public function removeItems(array $items)
    {
        foreach ($items as $item) {
            $this->removeItem($item);
        }
    }

    public function hasKey($key)
    {
        return $this->offsetExists($key);
    }

    public function getKey($item, $return_all = false)
    {
        $keys = array_keys($this->items, $item, true);
        if ($return_all) {
            return $keys;
        } else {
            return count($keys) > 0 ? $keys[0] : false;
        }
    }

    public function hasItem($item)
    {
        return $this->getKey($item) !== false;
    }

    public function getSize()
    {
        return $this->count();
    }

    public function addListener(IListener $listener)
    {
        if (!in_array($listener, $this->collection_listeners, true)) {
            $this->collection_listeners[] = $listener;
        }
    }

    public function removeListener(IListener $listener)
    {
        if (false !== ($pos = array_search($listener, $this->collection_listeners, true))) {
            array_splice($this->collection_listeners, $pos, 1);
        }
    }

    public function toArray()
    {
        return $this->items;
    }

    protected function propagateCollectionChangedEvent(CollectionChangedEvent $event)
    {
        foreach ($this->collection_listeners as $listener) {
            $listener->onCollectionChanged($event);
        }
    }
}
