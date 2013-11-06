<?php

namespace Dat0r;

abstract class TypedList extends ArrayList
{
    abstract protected function getItemImplementor();

    public function offsetSet($offset, $value)
    {
        $implementor = $this->getItemImplementor();
        if (!$value instanceof $implementor) {
            throw new Exception(
                sprintf(
                    "Items passed to the '%s' method must relate to '%s'."
                    . "%sAn instance of '%s' was given instead.",
                    __METHOD__,
                    $implementor,
                    PHP_EOL,
                    @get_class($value)
                )
            );
        }
        $this->items[$offset] = $value;
    }
}
