<?php

namespace Dat0r;

interface ICollection extends \Iterator, \Countable, \ArrayAccess
{
    public function getItem($key);

    public function getItems(array $keys);

    public function hasItem($item);

    public function hasKey($key);

    public function getKey($item, $return_all = false);

    public function getSize();

    public function filter(Callable $callback);

    public function removeItem($item);

    public function removeItems(array $items);
}
