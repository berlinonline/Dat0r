<?php

namespace Dat0r\Runtime\Attribute\Validator\Rule\Type;

use Dat0r\Runtime\Attribute\Validator\Rule\Rule;
use Dat0r\Runtime\Attribute\Validator\Result\IIncident;

class InArrayRule extends Rule
{
    protected function execute($value)
    {
        $allowed_values = $this->getOption('allowed_values', array());
        $allow_multiple = $this->getOption('allow_multiple', false);
        $max_values = $this->getOption('max', 0);
        $cast_to_array = $this->getOption('cast_to_array', true);

        $success = true;
        $casted = null;

        // Cast the incoming value to an array in order to streamline the further validation strategy.
        if (is_array($value)) {
            $casted = $value;
        } elseif (is_scalar($value)) {
            $casted = array($value);
        } else {
            $success = false;
            $this->throwError('invalid_type');
        }
        // Check if the given value(s) are valid/allowed.
        foreach ($casted as $current_value) {
            if (!in_array($current_value, $allowed_values)) {
                $this->throwError('invalid_value');
                $success = false;
            }
        }
        // Check that we don't exceed the maximum number of allowed values.
        if ($allow_multiple && ($max_values !== 0 && count($casted) <= $max_values)) {
            $this->throwError('too_many');
            $success = false;
        }
        // Export our transformed value (cast_to)
        if ($success) {
            $this->setSanitizedValue($cast_to_array ? $casted : $value);
        }

        return $success;
    }
}
