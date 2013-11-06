<?php

namespace Dat0r;

interface IList extends ICollection
{
    public function addItem($item);

    public function addItems(array $items);

    public function push($item);

    public function pop();

    public function shift();

    public function unshift($item);

    public function getFirst();

    public function getLast();
}
