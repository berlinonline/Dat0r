<?php

namespace Dat0r\Runtime\Attribute\KeyValueList;

use Dat0r\Runtime\ValueHolder\ValueHolder;

/**
 * Default implementation used for key-value containment.
 */
class KeyValueListValueHolder extends ValueHolder
{
    /**
     * Tells whether the given other_value is considered the same value as the
     * internally set value of this valueholder.
     *
     * @param array $other_value values to compare to the internal ones
     *
     * @return boolean true if the given value is considered the same value as the internal one
     */
    protected function valueEquals($other_value)
    {
        if (!is_array($other_value)) {
            return false;
        }

        /** @var array $numbers */
        $numbers = $this->getValue();

        $numbers_count = count($numbers);
        $other_count = count($other_value);

        if ($numbers_count !== $other_count) {
            return false;
        }

        foreach ($numbers as $idx => $val) {
            if ($other_value[$idx] !== $val) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns a (de)serializable representation of the internal value. The
     * returned format MUST be acceptable as a new value on the valueholder
     * to reconstitute it.
     *
     * @return mixed value that can be used for serializing/deserializing
     */
    public function toNative()
    {
        return $this->getValue();
    }

    /**
     * Sets the value holder's (int) value.
     *
     * @param string $value
     */
    public function setValue($value)
    {
        // @todo move to validator rule
        $attributes = array();
        $value = empty($value) ? array() : $value;
        foreach ($value as $key => $value) {
            $key = trim($key);
            if (!empty($key)) {
                $attributes[$key] = $this->castValue($value);
            }
        }

        return parent::setValue($attributes);
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
        $constraints = $this->getAttribute()->getOption(KeyValueListAttribute::OPTION_VALUE_CONSTRAINTS, array());
        $value_type = 'dynamic';

        if (isset($constraints['value_type'])) {
            $value_type = $constraints['value_type'];
        }

        return $value_type;
    }
}
