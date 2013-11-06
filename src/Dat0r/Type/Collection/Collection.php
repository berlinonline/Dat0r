<?php

namespace Dat0r\Type\Collection;

use Dat0r\Type\Object;

abstract class Collection extends Object implements ICollection
{
    protected $items;

    public function __construct()
    {
        $this->items = array();
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
        return $this->items[$offset];
    }

    /**
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetSet($offset, $value)
    {
        if ($this instanceof IUniqueCollection) {
            if (false !== ($item_key = array_search($value, $this->items, true))) {
                throw new Exception("Item allready has been added to the collection at key: " . $item_key);
            }
        }
        $this->items[$offset] = $value;
    }

    /**
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetUnset($offset)
    {
        array_splice($this->items, $offset, 1);
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
            throw new Exception('Key may not be null.');
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

    public function toArray()
    {
        return $this->items;
    }
}
