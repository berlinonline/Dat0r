<?php

namespace Dat0r\Runtime\Attribute\ValueHolder\Bundle;

use Dat0r\Runtime\Attribute\ValueHolder\ValueHolder;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\IAttribute;
use Dat0r\Runtime\Attribute\Bundle\Choice;

class ChoiceValueHolder extends ValueHolder
{
    /**
     * Can't compare selecr values by less or greater than clause.
     *
     * @param mixed $righthand_value
     *
     * @return boolean (always false)
     */
    public function isValueGreaterThan($righthand_value)
    {
        return false;
    }

    /**
     * Can't compare selecr values by less or bigger.
     *
     * @param mixed $righthand_value
     *
     * @return boolean (always false)
     */
    public function isValueLessThan($righthand_value)
    {
        return false;
    }

    /**
     * Tells if a given select value(list) is equal to the valueholder's current value.
     *
     * @param mixed $righthand_value
     *
     * @return boolean
     */
    public function isValueEqualTo($righthand_value)
    {
        $lefthand_value = $this->getValue();

        return $lefthand_value == $righthand_value;
    }
}
