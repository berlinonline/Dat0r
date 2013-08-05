<?php

namespace Dat0r\Core\ValueHolder;

use Dat0r\Core\Error;
use Dat0r\Core\Field\IField;
use Dat0r\Core\Field\KeyValueField;

/**
 * Default IValueHolder implementation used for key-value containment.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class KeyValueValueHolder extends ValueHolder
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
            foreach ($lefthand_value as $key => $value) {
                if ($righthand_value[$key] !== $value) {
                    $are_equal = false;
                }
            }
        } else {
            $are_equal = false;
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

        foreach ($value as $key => $value) {
            $key = trim($key);
            if (!empty($key)) {
                $attributes[$key] = $this->castValue($value);
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
        if (! ($field instanceof KeyValueField)) {
            throw new Error\BadValueException(
                "Only instances of KeyValueField my be associated with KeyValueValueHolder."
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
