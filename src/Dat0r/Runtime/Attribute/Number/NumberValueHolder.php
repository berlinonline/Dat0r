<?php

namespace Dat0r\Runtime\Attribute\Number;

use Dat0r\Runtime\ValueHolder\ValueHolder;

/**
 * Default implementation used for integer value containment.
 */
class NumberValueHolder extends ValueHolder
{
    /**
     * Tells whether the given other_value is considered the same value as the
     * internally set value of this valueholder.
     *
     * @param int $other_value number value to compare
     *
     * @return boolean true if the given value is considered the same value as the internal one
     */
    protected function valueEquals($other_value)
    {
        return $this->getValue() === $other_value;
    }
}
