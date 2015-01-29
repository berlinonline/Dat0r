<?php

namespace Dat0r\Runtime\Attribute\Boolean;

use Dat0r\Runtime\ValueHolder\ValueHolder;

/**
 * Default implementation used for boolean value containment.
 */
class BooleanValueHolder extends ValueHolder
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

    /**
     * Returns a (de)serializable representation of the internal value. The
     * returned format MUST be acceptable as a new value on the valueholder
     * to reconstitute it.
     *
     * @return mixed value that can be used for serializing/deserializing
     */
    public function toNative()
    {
        return $this->getValue();
    }
}
