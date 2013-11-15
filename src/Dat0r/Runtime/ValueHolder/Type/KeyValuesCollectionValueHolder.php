<?php

namespace Dat0r\Runtime\ValueHolder\Type;

use Dat0r\Runtime\ValueHolder\ValueHolder;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Field\IField;
use Dat0r\Runtime\Field\Type\KeyValuesCollectionField;

/**
 * Default IValueHolder implementation used for key-values collection value containment.
 */
class KeyValuesCollectionValueHolder extends ValueHolder
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
        $lefthand_count = 0;
        $righthand_count = 0;

        if (is_array($lefthand_value)) {
            $lefthand_count = count($lefthand_value);
        }
        if (is_array($righthand_value)) {
            $righthand_count = count($righthand_value);
        }

        return $lefthand_count > $righthand_count;
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
        $lefthand_count = 0;
        $righthand_count = 0;

        if (is_array($lefthand_value)) {
            $lefthand_count = count($lefthand_value);
        }
        if (is_array($righthand_value)) {
            $righthand_count = count($righthand_value);
        }

        return $lefthand_count > $righthand_count;
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
        $lefthand_value = $this->getValue();
        $lefthand_count = 0;
        $righthand_count = 0;
        $are_equal = true;

        if (is_array($lefthand_value)) {
            $lefthand_count = count($lefthand_value);
        }
        if (is_array($righthand_value)) {
            $righthand_count = count($righthand_value);
        }

        if (0 < $lefthand_count && $lefthand_count === $righthand_count) {
            foreach ($lefthand_value as $idx => $text) {
                if ($righthand_value[$idx] !== $text) {
                    $are_equal = false;
                }
            }
        } elseif ($lefthand_count !== $righthand_count) {
            $are_equal = false;
        } else {
            foreach ($lefthand_value as $key => $values) {
                if (! isset($righthand_value[$key])) {
                    $are_equal = false;
                    break;
                }

                foreach ($values as $idx => $curValue) {
                    if (isset($righthand_value[$key][$idx]) !== $curValue) {
                        $are_equal = false;
                        break;
                    }
                }
            }
        }

        return $are_equal;
    }

    /**
     * Sets the value holder's (int) value.
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $attributes = array();
        $value = empty($value) ? array() : $value;
        foreach ($value as $key => $values) {
            $key = trim($key);
            if (! empty($key)) {
                $attributes[$key] = array();
                foreach ($values as $curValue) {
                    $curValue = trim($curValue);
                    if (! empty($curValue)) {
                        $attributes[$key][] = $this->castValue($curValue);
                    }
                }
            }
        }

        return parent::setValue($attributes);
    }

    protected function castValue($value)
    {
        $value_type = $this->getValueTypeConstraint();
        $valid_values = true;

        switch ($value_type) {
            case 'integer':
                $value = (int)$value;
                break;

            case 'string':
                $value = (string)$value;
                break;

            case 'boolean':
                $value = (bool)$value;
                break;
        }

        return $value;
    }

    protected function getValueTypeConstraint()
    {
        $constraints = $this->getField()->getOption(KeyValuesCollectionField::OPT_VALUE_CONSTRAINT, array());
        $value_type = 'dynamic';

        if (isset($constraints['value_type'])) {
            $value_type = $constraints['value_type'];
        }

        return $value_type;
    }
}
