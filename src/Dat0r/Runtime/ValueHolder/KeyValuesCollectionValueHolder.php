<?php

namespace Dat0r\Runtime\ValueHolder;

use Dat0r\Runtime\Error;
use Dat0r\Runtime\Field\IField;
use Dat0r\Runtime\Field\KeyValuesCollectionField;

/**
 * Default IValueHolder implementation used for key-values collection value containment.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
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
    public function isGreaterThan(IValueHolder $other)
    {
        $lefthand_value = $this->getValue();
        $righthand_value = $other->getValue();
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
    public function isLessThan(IValueHolder $other)
    {
        $lefthand_value = $this->getValue();
        $righthand_value = $other->getValue();
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
    public function isEqualTo(IValueHolder $other)
    {
        $lefthand_value = $this->getValue();
        $righthand_value = $other->getValue();
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

        parent::setValue($attributes);
    }

    /**
     * Contructs a new TextValueHolder instance from a given value.
     *
     * @param IField $field
     * @param mixed $value
     */
    protected function __construct(IField $field, $value = null)
    {
        if (!($field instanceof KeyValuesCollectionField)) {
            throw new Error\BadValueException(
                "Only instances of KeyValuesCollectionField my be associated with KeyValuesCollectionValueHolder."
            );
        }

        parent::__construct($field, $value);
    }

    protected function castValue($value)
    {
        $value_type = $this->getField()->getValueTypeConstraint();
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
}
