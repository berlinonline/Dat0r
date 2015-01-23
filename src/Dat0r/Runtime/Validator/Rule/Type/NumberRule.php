<?php

namespace Dat0r\Runtime\Validator\Rule\Type;

use Dat0r\Runtime\Validator\Rule\Rule;
use Dat0r\Runtime\Validator\Result\IIncident;

class NumberRule extends Rule
{
    protected function execute($value)
    {
        $success = true;

        if (!is_scalar($value)) {
            $this->throwError('non_scalar', array(), IIncident::CRITICAL);
            return false;
        }

        if (!is_numeric($value)) {
            $this->throwError('invalid_type');
            $success = false;
        }

        if ($success && $this->getOption('cast_to') === 'float') {
            $value = (float)filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
        } elseif ($success) {
            $value = (int)filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        }
        $this->setSanitizedValue($value);

        return $success;
    }
}
