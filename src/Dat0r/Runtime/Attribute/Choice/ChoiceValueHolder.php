<?php

namespace Dat0r\Runtime\Attribute\Choice;

use Dat0r\Runtime\ValueHolder\ValueHolder;

class ChoiceValueHolder extends ValueHolder
{
    /**
     * Tells whether the given other_value is considered the same value as the
     * internally set value of this valueholder.
     *
     * @param boolean $other_value value to compare
     *
     * @return boolean true if the given value is considered the same value as the internal one
     */
    protected function valueEquals($other_value)
    {
        return $this->getValue() === $other_value;
    }
}
