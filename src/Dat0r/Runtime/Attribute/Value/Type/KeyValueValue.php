<?php

namespace Dat0r\Runtime\Attribute\Value\Type;

use Dat0r\Runtime\Attribute\Value\Value;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\AttributeInterface;
use Dat0r\Runtime\Attribute\Type\KeyValue;

/**
 * Default ValueInterface implementation used for key-value containment.
 */
class KeyValueValue extends Value
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
            foreach ($lefthand_value as $key => $value) {
                if ($other_value[$key] !== $value) {
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
    public function set($value)
    {
        // @todo move to validator
        $attributes = array();
        $value = empty($value) ? array() : $value;
        foreach ($value as $key => $value) {
            $key = trim($key);
            if (!empty($key)) {
                $attributes[$key] = $this->castValue($value);
            }
        }

        return parent::set($attributes);
    }

    protected function castValue($value)
    {
        $value_type = $this->getTypeConstraint();

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

    public function getTypeConstraint()
    {
        $constraints = $this->getAttribute()->getOption(KeyValue::OPT_VALUE_CONSTRAINT, array());
        $value_type = 'dynamic';

        if (isset($constraints['value_type'])) {
            $value_type = $constraints['value_type'];
        }

        return $value_type;
    }
}
