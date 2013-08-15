<?php

namespace Dat0r\Core\Validator;

/**
 * Default implementation for validators that validate boolean values.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class BooleanValidator extends Validator
{
    /**
     * Validates a given value thereby considering the state of the field
     * that a specific validator instance is related to.
     *
     * @param mixed $value
     *
     * @return boolean
     */
    public function validate($value)
    {
        $casted_value = $value;

        if (is_bool($casted_value)) {
            // noop
        } elseif (1 === $casted_value || '1' === $casted_value) {
            $casted_value = true;
        } elseif (0 === $casted_value || '0' === $casted_value) {
            $casted_value = false;
        } elseif (empty($casted_value)) {
            $casted_value = false;
        }

        return is_bool($casted_value);
    }
}
