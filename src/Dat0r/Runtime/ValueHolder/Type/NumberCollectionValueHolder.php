<?php

namespace Dat0r\Runtime\ValueHolder\Type;

use Dat0r\Runtime\ValueHolder\ValueHolder;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\IAttribute;
use Dat0r\Runtime\Attribute\Type\NumberCollection;

/**
 * Default IValueHolder implementation used for integer collection value containment.
 */
class NumberCollectionValueHolder extends ValueHolder
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
        // @todo move to validator
        $values = array();
        $value = empty($value) ? array() : $value;
        foreach ($value as $int) {
            if (! empty($int)) {
                $values[] = (int)$int;
            }
        }

        return parent::setValue($values);
    }
}
