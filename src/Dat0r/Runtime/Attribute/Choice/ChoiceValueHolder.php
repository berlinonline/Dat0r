<?php

namespace Dat0r\Runtime\Attribute\Choice;

use Dat0r\Runtime\ValueHolder\ValueHolder;

class ChoiceValueHolder extends ValueHolder
{
    /**
     * Tells if a given select value(list) is equal to the valueholder's current value.
     *
     * @param mixed $other_value
     *
     * @return boolean
     */
    public function isEqualTo($other_value)
    {
        return $this->get() === $other_value;
    }
}
