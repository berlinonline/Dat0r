<?php

namespace Dat0r\Runtime\Attribute\Value\Type;

use Dat0r\Runtime\Attribute\Value\Value;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\AttributeInterface;
use Dat0r\Runtime\Attribute\Type\Number;

/**
 * Default ValueInterface implementation used for integer value containment.
 */
class NumberValue extends Value
{
    /**
     * Tells whether a spefic ValueInterface instance's value is considered equal to
     * the value of an other given ValueInterface.
     *
     * @param ValueInterface $other
     *
     * @return boolean
     */
    public function isEqualTo($other_value)
    {
        return $this->get() === $other_value;
    }
}
