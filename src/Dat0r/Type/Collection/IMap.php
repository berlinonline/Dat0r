<?php

namespace Dat0r\Type\Collection;

interface IMap extends ICollection
{
    public function setItem($key, $item);

    public function setItems(array $items);

    public function getKeys();

    public function getValues();
}
