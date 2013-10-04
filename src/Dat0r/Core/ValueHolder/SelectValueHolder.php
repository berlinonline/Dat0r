<?php

namespace Dat0r\Core\ValueHolder;

use Dat0r\Core\Error;
use Dat0r\Core\Field\IField;
use Dat0r\Core\Field\SelectField;

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
    public function isGreaterThan(IValueHolder $other)
    {
        $lefthand_value = $this->getValue();
        $righthand_value = $other->getValue();

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
    public function isLessThan(IValueHolder $other)
    {
        $lefthand_value = $this->getValue();
        $righthand_value = $other->getValue();

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
    public function isEqualTo(IValueHolder $other)
    {
        $lefthand_value = $this->getValue();
        $righthand_value = $other->getValue();

        return $lefthand_value == $righthand_value;
    }

    public function setValue($value)
    {
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

        parent::setValue($value);
    }

    protected function __construct(IField $field, $value = null)
    {
        if (!($field instanceof SelectField)) {
            throw new Error\BadValueException(
                "Only instances of SelectField my be associated with SelectValueHolder."
            );
        }

        parent::__construct($field, $value);
    }
}
