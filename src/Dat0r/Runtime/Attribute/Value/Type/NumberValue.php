<?php

namespace Dat0r\Runtime\Attribute\Value\Type;

use Dat0r\Runtime\Attribute\Value\Value;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\IAttribute;
use Dat0r\Runtime\Attribute\Type\Number;

/**
 * Default IValue implementation used for integer value containment.
 */
class NumberValue extends Value
{
    /**
     * Tells whether a spefic IValue instance's value is considered equal to
     * the value of an other given IValue.
     *
     * @param IValue $other
     *
     * @return boolean
     */
    public function isEqualTo($other_value)
    {
        return $this->get() === $other_value;
    }
}
