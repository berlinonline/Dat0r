<?php

namespace Dat0r\Runtime\Attribute\Value\Type;

use Dat0r\Runtime\Attribute\Value\Value;

/**
 * Default ValueInterface implementation used for text value containment.
 */
class TextValue extends Value
{
    /**
     * Tells whether a spefic ValueInterface instance's value is considered equal to
     * the value of an other given ValueInterface.
     *
     * @param ValueInterface $other
     *
     * @return boolean
     */
    public function isEqualTo($right_value)
    {
        return $this->get() === $right_value;
    }
}
