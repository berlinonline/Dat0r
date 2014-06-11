<?php

namespace Dat0r\Runtime\ValueHolder\Type;

use Dat0r\Runtime\ValueHolder\ValueHolder;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\IAttribute;
use Dat0r\Runtime\Attribute\Type\Text;

/**
 * Default IValueHolder implementation used for text value containment.
 */
class TextValueHolder extends ValueHolder
{
    /**
     * Tells whether a spefic IValueHolder instance's value is considered greater than
     * the value of an other given IValueHolder.
     *
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isValueGreaterThan($right_value)
    {
        return 0 < strcmp($this->getValue(), $right_value);
    }

    /**
     * Tells whether a spefic IValueHolder instance's value is considered less than
     * the value of an other given IValueHolder.
     *
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isValueLessThan($right_value)
    {
        return 0 > strcmp($this->getValue(), $right_value);
    }

    /**
     * Tells whether a spefic IValueHolder instance's value is considered equal to
     * the value of an other given IValueHolder.
     *
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isValueEqualTo($right_value)
    {
        return $this->getValue() === $right_value;
    }
}
