<?php

namespace Dat0r\Runtime\Attribute\NumberList;

use Dat0r\Runtime\ValueHolder\ValueHolder;

/**
 * Default implementation used for a list of integer values.
 */
class NumberListValueHolder extends ValueHolder
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
     * Sets the value holder's (int) value.
     *
     * @param string $value
     */
    public function setValue($value)
    {
        // @todo move to validator rule
        $values = array();
        $value = !is_array($value) || empty($value) ? array() : $value;
        foreach ($value as $int) {
            if (! empty($int)) {
                $values[] = (int)$int;
            }
        }

        return parent::setValue($values);
    }
}
