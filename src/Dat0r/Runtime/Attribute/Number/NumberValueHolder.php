<?php

namespace Dat0r\Runtime\Attribute\Number;

use Dat0r\Runtime\ValueHolder\ValueHolder;

/**
 * Default implementation used for integer value containment.
 */
class NumberValueHolder extends ValueHolder
{
    /**
     * Tells whether a specific ValueHolderInterface instance's value is considered equal to
     * the value of an other given ValueHolderInterface.
     *
     * @param ValueHolderInterface $other
     *
     * @return boolean
     */
    public function isEqualTo($other_value)
    {
        return $this->getValue() === $other_value;
    }
}
