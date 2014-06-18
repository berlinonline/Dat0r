<?php

namespace Dat0r\Runtime\Attribute\Value\Type;

use Dat0r\Runtime\Attribute\Value\Value;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\IAttribute;
use Dat0r\Runtime\Attribute\Type\Text;

/**
 * Default IValue implementation used for text value containment.
 */
class TextValue extends Value
{
    /**
     * Tells whether a spefic IValue instance's value is considered equal to
     * the value of an other given IValue.
     *
     * @param IValue $other
     *
     * @return boolean
     */
    public function isEqualTo($right_value)
    {
        return $this->get() === $right_value;
    }
}
