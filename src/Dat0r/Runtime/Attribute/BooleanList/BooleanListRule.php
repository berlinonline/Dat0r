<?php

namespace Dat0r\Runtime\Attribute\BooleanList;

use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;

class BooleanListRule extends Rule
{
    protected function execute($values)
    {
        if (!is_array($values)) {
            $this->throwError('non_array_value', [], IncidentInterface::CRITICAL);
            return false;
        }

        $sanitized = [];

        foreach ($values as $value) {
            $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            if (null === $bool || is_object($value) || $value === "" || $value === null) {
                // FILTER_VALIDATE_BOOLEAN treats objects, NULL and empty strings as boolean FALSE… -.-
                $this->throwError('invalid_type', [ 'value' => $value ]);
                return false;
            }

            $sanitized[] = $bool;
        }

        $this->setSanitizedValue($sanitized);

        return true;
    }
}
