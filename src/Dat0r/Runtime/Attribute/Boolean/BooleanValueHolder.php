<?php

namespace Dat0r\Runtime\Attribute\Boolean;

use Dat0r\Runtime\ValueHolder\ValueHolder;
use Dat0r\Common\Error\BadValueException;

/**
 * Default implementation used for boolean value containment.
 */
class BooleanValueHolder extends ValueHolder
{
    /**
     * Tells if a given boolean value is equal to an other given boolean.
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
