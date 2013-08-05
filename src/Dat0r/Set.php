<?php

namespace Dat0r;

class Set extends ArrayList implements ISet
{
    public function offsetSet($offset, $value)
    {
        foreach ($this->items as $index => $item) {
            if ($item === $value) {
                $offset = $index;
                break;
            }
        }

        parent::offsetSet($offset, $value);
    }
}
