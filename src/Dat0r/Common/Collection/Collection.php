<?php

namespace Dat0r\Common\Collection;

use Dat0r\Common\Object;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Common\Error\BadValueException;

/**
 * Generic base implementation of the ICollection interface.
 */
abstract class Collection extends Object implements ICollection
{
    /**
     * An array of IListener that are notified upon collection changes.
     * We can't (re)use our ICollection stuff here as this is the lowest level of it's implementation.
     *
     * @var array
     */
    private $collection_listeners;

    /**
     * Holds the collection's current items.
     *
     * @var array
     */
    protected $items;

    /**
     * Creates/constructs a new collection instance.
     */
    public function __construct()
    {
        $this->collection_listeners = array();
        $this->items = array();
    }

    // Php Interface - Countable

    /**
     * Implementation of php's 'countable' interface's 'count' method.
     *
     * @return int
     *
     * @see http://php.net/manual/en/class.countable.php
     */
    public function count()
    {
        return count($this->items);
    }

    // Php Interface - ArrayAccess

    /**
     * Tells whether or not an offset exists.
     *
     * @param mixed $offset
     *
     * @return boolean
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * Returns the value at specified offset.
     *
     * @param mixed $offset
     *
     * @return mixed
     *
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
     * Assigns a value to the specified offset.
     *
     * @param mixed $offset
     * @param mixed $value
     *
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
     * Unsets the value at the given offset.
     *
     * @param mixed $offset
     *
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
     * Returns the key for our current internal-pointer position.
     *
     * @return mixed
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function key()
    {
        return key($this->items);
    }

    // Php Interface - Iterator

    /**
     * Tells if our current internal-pointer position is valid.
     *
     * @return boolean
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function valid()
    {
        return null !== $this->key();
    }

    /**
     * Returns the value for our current internal-pointer position.
     *
     * @return mixed
     *
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
     * Forward our current internal-pointer to it's next position.
     *
     * @return mixed
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function next()
    {
        return next($this->items);
    }

    /**
     * (Re)set our current internal-pointer position to the start.
     *
     * @return mixed
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function rewind()
    {
        return reset($this->items);
    }

    // Interface - ICollection

    /**
     * Return a specific item from the collection for the given key.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function getItem($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * Return a list of specific items from the collection for the given keys.
     *
     * @param array $keys
     *
     * @return mixed
     */
    public function getItems(array $keys)
    {
        $items = array();
        foreach ($keys as $key) {
            $items[] = $this->offsetGet($key);
        }

        return $items;
    }

    /**
     * Add an item for the given key to the collection.
     *
     * @param mixed $key
     * @param mixed $item
     */
    public function setItem($key, $item)
    {
        if ($key === null) {
            throw new BadValueException('Key may not be null.');
        }
        $this->offsetSet($key, $item);
    }

    /**
     * Remove the given item from the collection.
     *
     * @param mixed $item
     */
    public function removeItem($item)
    {
        $this->offsetUnset($this->getKey($item));
    }

    /**
     * Remove the given items from the collection.
     *
     * @param array $items
     */
    public function removeItems(array $items)
    {
        foreach ($items as $item) {
            $this->removeItem($item);
        }
    }

    /**
     * Tells if the collection has an item set for the given key.
     *
     * @param mixed $key
     *
     * @return boolean
     */
    public function hasKey($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Return the key for the given item.
     * If the collection contains the given item more than once,
     * the first key will be returned.
     * If you wish to receive all set the '$return_all' parameter to true.
     *
     * @param mixed $item
     * @param boolean $return_true
     *
     * @return mixed Returns false, if the item is not contained.
     */
    public function getKey($item, $return_all = false)
    {
        $keys = array_keys($this->items, $item, true);
        if ($return_all) {
            return $keys;
        } else {
            return count($keys) > 0 ? $keys[0] : false;
        }
    }

    /**
     * Tells if the collection contains the given item.
     *
     * @param mixed $item
     *
     * @return boolean
     */
    public function hasItem($item)
    {
        return $this->getKey($item) !== false;
    }

    /**
     * Returns the collection size.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->count();
    }

    /**
     * Attaches the given listener, so it will be informed about all future changes.
     *
     * @param IListener $listener
     */
    public function addListener(IListener $listener)
    {
        if (!in_array($listener, $this->collection_listeners, true)) {
            $this->collection_listeners[] = $listener;
        }
    }

    /**
     * Removes the given listener from our list of collection-changed listeners.
     *
     * @param IListener $listener
     */
    public function removeListener(IListener $listener)
    {
        if (false !== ($pos = array_search($listener, $this->collection_listeners, true))) {
            array_splice($this->collection_listeners, $pos, 1);
        }
    }

    /**
     * Returns the collection's underlying array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * Propagate the given collection-changed event to all currently attached listeners.
     */
    protected function propagateCollectionChangedEvent(CollectionChangedEvent $event)
    {
        foreach ($this->collection_listeners as $listener) {
            $listener->onCollectionChanged($event);
        }
    }
}
