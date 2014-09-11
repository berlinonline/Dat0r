<?php

namespace Dat0r\Common\Collection;

use Closure;

interface ICollection extends \Iterator, \Countable, \ArrayAccess
{
    public function getItem($key);

    public function getItems(array $keys);

    public function hasItem($item);

    public function hasKey($key);

    public function getKey($item, $return_all = false);

    public function getSize();

    public function clear();

    public function append(ICollection $collection);

    public function filter(Closure $callback);

    public function removeItem($item);

    public function removeItems(array $items);

    public function addListener(IListener $listener);

    public function removeListener(IListener $listener);
}
