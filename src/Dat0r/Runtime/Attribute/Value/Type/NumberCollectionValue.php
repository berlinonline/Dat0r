<?php

namespace Dat0r\Runtime\Attribute\Value\Type;

use Dat0r\Runtime\Attribute\Value\Value;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\AttributeInterface;
use Dat0r\Runtime\Attribute\Type\NumberCollection;

/**
 * Default ValueInterface implementation used for integer collection value containment.
 */
class NumberCollectionValue extends Value
{
    /**
     * Tells whether a spefic ValueInterface instance's value is considered equal to
     * the value of an other given ValueInterface.
     *
     * @param ValueInterface $other
     *
     * @return boolean
     */
    public function isEqualTo($other_value)
    {
        /** @var array $lefthand_value */
        $lefthand_value = $this->get();
        $lefthand_count = 0;
        $righthand_count = 0;
        $are_equal = true;

        if (is_array($lefthand_value)) {
            $lefthand_count = count($lefthand_value);
        }
        if (is_array($other_value)) {
            $righthand_count = count($other_value);
        }

        if (0 < $lefthand_count && $lefthand_count === $righthand_count) {
            foreach ($lefthand_value as $idx => $text) {
                if ($other_value[$idx] !== $text) {
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
    public function set($value)
    {
        // @todo move to validator
        $values = array();
        $value = !is_array($value) || empty($value) ? array() : $value;
        foreach ($value as $int) {
            if (! empty($int)) {
                $values[] = (int)$int;
            }
        }

        return parent::set($values);
    }
}
