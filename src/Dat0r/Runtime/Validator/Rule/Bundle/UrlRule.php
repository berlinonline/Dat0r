<?php

namespace Dat0r\Runtime\Validator\Rule\Bundle;

use Dat0r\Runtime\Validator\Rule\Rule;
use Dat0r\Runtime\Validator\Result\IIncident;

class UrlRule extends Rule
{
    protected function execute($value)
    {
        if (!is_string($value)) {
            $this->throwError('invalid_type', array(), IIncident::CRITICAL);
            return false;
        }

        $success = filter_var($value, FILTER_VALIDATE_URL);
        if (!$success) {
            $this->throwError('invalid_format');
            $success = false;
        } else {
            $this->setSanitizedValue($value);
        }

        return $success;
    }
}
