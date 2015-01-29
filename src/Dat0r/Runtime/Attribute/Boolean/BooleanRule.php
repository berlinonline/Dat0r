<?php

namespace Dat0r\Runtime\Attribute\Boolean;

use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;

class BooleanRule extends Rule
{
    protected function execute($value)
    {
        $success = true;

        if (!is_bool($value)) {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
        if (null === $value) {
            $success = false;
            $this->throwError('invalid_type');
        }

        if ($success && is_bool($value)) {
            $this->setSanitizedValue($value);
        }

        return $success;
    }
}
