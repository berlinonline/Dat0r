<?php

namespace Dat0r\Runtime\Attribute\IntegerList;

use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;

class IntegerListRule extends Rule
{
    protected function execute($values)
    {
        if (!is_array($values)) {
            $this->throwError('non_array_value', [], IncidentInterface::CRITICAL);
            return false;
        }

        $sanitized = [];

        foreach ($values as $int) {
            $value = $int;
            $cast = $this->toBoolean($this->getOption(IntegerListAttribute::OPTION_CAST_STRINGS_TO_INTEGER, true));
            if (is_string($value) && $cast === true) {
                $value = $this->toBoolean($value);
            }

            if (!is_int($value)) {
                $this->throwError('non_integer_value', [ 'value' => $value ], IncidentInterface::CRITICAL);
                return false;
            }

            // check minimum value
            if ($min = $this->getOption(IntegerListAttribute::OPTION_MIN, false)) {
                if ($value < (int)$min) {
                    $this->throwError(IntegerListAttribute::OPTION_MIN, [
                        IntegerListAttribute::OPTION_MIN => (int)$min,
                        'value' => $value
                    ]);
                    return false;
                }
            }

            // check maximum value
            if ($max = $this->getOption(IntegerListAttribute::OPTION_MAX, false)) {
                if ($value > (int)$max) {
                    $this->throwError(IntegerListAttribute::OPTION_MAX, [
                        IntegerListAttribute::OPTION_MAX => (int)$max,
                        'value' => $value
                    ]);
                    return false;
                }
            }

            $sanitized[] = $value;
        }

        $this->setSanitizedValue($sanitized);

        return true;
    }
}
