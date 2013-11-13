<?php

namespace Dat0r\Runtime\ValueHolder;

use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Field\IField;
use Dat0r\Runtime\Field\BooleanField;

/**
 * Default IValueHolder implementation used for boolean value containment.
 */
class BooleanValueHolder extends ValueHolder
{
    /**
     * Tells whether a spefic IValueHolder instance's value is considered greater than
     * the value of an other given IValueHolder.
     *
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isGreaterThan($righthand_value)
    {
        return true === $this->getValue() && false === $righthand_value;
    }

    /**
     * Tells whether a spefic IValueHolder instance's value is considered less than
     * the value of an other given IValueHolder.
     *
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isLessThan($righthand_value)
    {
        return false === $this->getValue() && true === $righthand_value;
    }

    /**
     * Tells whether a spefic IValueHolder instance's value is considered equal to
     * the value of an other given IValueHolder.
     *
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isEqualTo($righthand_value)
    {
        return $this->getValue() === $righthand_value;
    }
}
