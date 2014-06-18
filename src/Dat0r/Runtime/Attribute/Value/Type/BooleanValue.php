<?php

namespace Dat0r\Runtime\Attribute\Value\Type;

use Dat0r\Runtime\Attribute\Value\Value;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\IAttribute;
use Dat0r\Runtime\Attribute\Type\Boolean;

/**
 * Default IValue implementation used for boolean value containment.
 */
class BooleanValue extends Value
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
