<?php

namespace Dat0r\Generic;

interface ICollection extends IObject, \Iterator, \Countable, \ArrayAccess
{
    public function add($item);

    public function addMore(array $items);

    public function remove($item);

    public function removeMore(array $items);

    public function getFirst();

    public function getLast();

    public function getSize();

    public function hasKey($key);

    public function getKey($item);

    public function has($item);
}
