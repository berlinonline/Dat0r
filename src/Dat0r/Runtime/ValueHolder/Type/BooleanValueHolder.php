<?php

namespace Dat0r\Runtime\ValueHolder\Type;

use Dat0r\Runtime\ValueHolder\ValueHolder;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Field\IField;
use Dat0r\Runtime\Field\Type\BooleanField;

/**
 * Default IValueHolder implementation used for boolean value containment.
 */
class BooleanValueHolder extends ValueHolder
{
    /**
     * Tells if a given boolean value is greater than an other given boolean.
     * With 'true' being greater than 'false'.
     *
     * @param mixed $righthand_value
     *
     * @return boolean
     */
    public function isValueGreaterThan($righthand_value)
    {
        return true === $this->getValue() && false === $righthand_value;
    }

    /**
     * Tells if a given boolean value is less than an other given boolean.
     * With 'true' being greater than 'false'.
     *
     * @param mixed $righthand_value
     *
     * @return boolean
     */
    public function isValueLessThan($righthand_value)
    {
        return false === $this->getValue() && true === $righthand_value;
    }

    /**
     * Tells if a given boolean value is equal to an other given boolean.
     *
     * @param mixed $righthand_value
     *
     * @return boolean
     */
    public function isValueEqualTo($righthand_value)
    {
        return $this->getValue() === $righthand_value;
    }
}
