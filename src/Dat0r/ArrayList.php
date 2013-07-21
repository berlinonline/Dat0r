<?php

namespace Dat0r;

class ArrayList extends Object implements ICollection
{
    const ITEMS = 'items';

    const ITEM_IMPLEMENTOR = 'item_implementor';

    protected $items;

    protected $item_implementor;

    public static function create(array $data = array())
    {
        $list = new static();

        $list->applyParameters($data);

        if (isset($data[self::ITEMS]))
        {
            $list->addMore($data[self::ITEMS]);
        }

        return $list;
    }

    public function add($item)
    {
        $this->offsetSet($this->count(), $item);
    }

    public function addMore(array $items)
    {
        foreach ($items as $item)
        {
            $this->add($item);
        }
    }

    public function remove($item)
    {
        $this->offsetUnset($this->getKey($item));
    }

    public function removeMore(array $items)
    {
        foreach ($items as $item)
        {
            $this->remove($item);
        }
    }

    public function getFirst()
    {
        $keys = array_keys($this->items);
        $first_key = reset($keys);

        if ($first_key !== false)
        {
            return $this->items[$first_key];
        }

        return null;
    }

    public function getLast()
    {
        $keys = array_keys($this->items);
        $last_key = end($keys);

        if ($last_key !== false)
        {
            return $this->items[$last_key];
        }

        return null;
    }

    public function getSize()
    {
        return $this->count();
    }

    public function hasKey($key)
    {
        return $this->offsetExists($key);
    }

    public function getKey($item)
    {
        return array_search($item, $this->items, true);
    }

    public function has($item)
    {
        return $this->getKey($item) !== false;
    }

    public function count()
    {
        return count($this->items);
    }

    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (!$value instanceof $this->item_implementor)
        {
            throw new Exception(
                sprintf(
                    "Items passed to the '%s' method must relate to '%s'."
                        . "%sAn instance of '%s' was given instead.",
                    __METHOD__,
                    $this->item_implementor,
                    PHP_EOL,
                    get_class($value)
                )
            );
        }

        $this->items[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        array_splice($this->items, $offset, 1);
    }

    public function current()
    {
        if ($this->valid())
        {
            return current($this->items);
        }
        else
        {
            return FALSE;
        }
    }

    public function key()
    {
        return key($this->items);
    }

    public function next()
    {
        return next($this->items);
    }

    public function rewind()
    {
        reset($this->items);
    }

    public function valid()
    {
        return NULL !== key($this->items);
    }

    protected function __construct()
    {
        $this->items = array();
    }

    protected function applyParameters(array $parameters = array())
    {
        if (!isset($parameters[self::ITEM_IMPLEMENTOR]))
        {
            throw new Exception(
                sprintf(
                    "Missing key '%s' for parameters given to '%s'.",
                    self::ITEM_IMPLEMENTOR,
                    __METHOD__
                )
            );
        }

        if (!class_exists($parameters[self::ITEM_IMPLEMENTOR]))
        {
            throw new Exception(
                sprintf(
                    "Unable to find class '%s' for '%s' given to '%s'.",
                    $parameters[self::ITEM_IMPLEMENTOR],
                    self::ITEM_IMPLEMENTOR,
                    __METHOD__
                )
            );
        }

        $this->item_implementor = $parameters[self::ITEM_IMPLEMENTOR];
    }
}
