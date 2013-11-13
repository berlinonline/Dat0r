<?php

namespace Dat0r\Runtime\Validation\Rule;

use Dat0r\Runtime\Validation\Result\IIncident;

class EmailRule extends Rule
{
    protected function execute($value)
    {
        if (!is_scalar($value)) {
            $this->throwError('invalid_type', array(), IIncident::CRITICAL);
            return false;
        }

        $success = filter_var($value, FILTER_VALIDATE_EMAIL);
        if (!$success) {
            $this->throwError('invalid_format');
            $success = false;
        } else {
            $this->setSanitizedValue($value);
        }

        return $success;
    }
}
