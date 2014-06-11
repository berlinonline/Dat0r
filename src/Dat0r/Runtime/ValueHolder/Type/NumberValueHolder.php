<?php

namespace Dat0r\Runtime\ValueHolder\Type;

use Dat0r\Runtime\ValueHolder\ValueHolder;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\IAttribute;
use Dat0r\Runtime\Attribute\Type\Number;

/**
 * Default IValueHolder implementation used for integer value containment.
 */
class NumberValueHolder extends ValueHolder
{
    /**
     * Tells whether a spefic IValueHolder instance's value is considered greater than
     * the value of an other given IValueHolder.
     *
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isValueGreaterThan($righthand_value)
    {
        $lefthand_value = $this->getValue();

        return $lefthand_value > $righthand_value;
    }

    /**
     * Tells whether a spefic IValueHolder instance's value is considered less than
     * the value of an other given IValueHolder.
     *
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isValueLessThan($righthand_value)
    {
        $lefthand_value = $this->getValue();

        return $lefthand_value < $righthand_value;
    }

    /**
     * Tells whether a spefic IValueHolder instance's value is considered equal to
     * the value of an other given IValueHolder.
     *
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isValueEqualTo($righthand_value)
    {
        return $this->getValue() === $righthand_value;
    }
}
