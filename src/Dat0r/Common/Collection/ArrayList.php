<?php

namespace Dat0r\Common\Collection;

use Closure;

/**
 * ArrayList should actually be named List, but php has this as a reserved token (T_LIST),
 * to support the '$what, $for = list($arr)' language construct.
 * Php, y U no CASE-sensitive?! (╯°□°）╯︵ ┻━┻)
 */
class ArrayList extends Collection implements IList
{
    public function __construct(array $items = array())
    {
        parent::__construct();

        $this->addItems($items);
    }

    // ICollection

    public function filter(Closure $callback)
    {
        $filtered_list = static::create();

        foreach ($this->items as $item) {
            if ($callback($item) === true) {
                $filtered_list->push($item);
            }
        }

        return $filtered_list;
    }

    // IList

    public function addItem($item)
    {
        $this->push($item);
    }

    public function addItems(array $items)
    {
        foreach ($items as $item) {
            $this->push($item);
        }
    }

    public function push($item)
    {
        $this->offsetSet($this->count(), $item);
    }

    public function pop()
    {
        return array_pop($this->items);
    }

    public function shift()
    {
        return array_shift($this->items);
    }

    public function unshift($item)
    {
        return array_unshift($this->items, $item);
    }

    public function getFirst()
    {
        if ($this->getSize() > 0) {
            return $this->items[0];
        }
        return null;
    }

    public function getLast()
    {
        $item_count = $this->getSize();
        if ($item_count > 0) {
            return $this->items[$item_count - 1];
        }
        return null;
    }
}
