<?php

namespace Dat0r\Runtime\ValueHolder;

use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Field\IField;
use Dat0r\Runtime\Field\SelectField;

class SelectValueHolder extends ValueHolder
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
    public function isLessThan($righthand_value)
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
    public function isEqualTo($righthand_value)
    {
        $lefthand_value = $this->getValue();

        return $lefthand_value == $righthand_value;
    }

    public function setValue($value)
    {
        // @todo move to validator
        if ($this->getField()->getOption('multiple', false)) {
            $selected_options = array();
            $value = empty($value) ? array() : $value;
            foreach ($value as $option) {
                $option = trim((string)$option);
                if (!empty($option)) {
                    $selected_options[] = $option;
                }
            }
            $value = $selected_options;
        }

        return parent::setValue($value);
    }
}
