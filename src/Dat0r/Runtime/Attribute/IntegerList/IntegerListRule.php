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

        $allow_hex = $this->toBoolean($this->getOption(IntegerListAttribute::OPTION_ALLOW_HEX, false));
        $allow_octal = $this->toBoolean($this->getOption(IntegerListAttribute::OPTION_ALLOW_OCTAL, false));

        $filter_flags = 0;
        if ($allow_hex) {
            $filter_flags |= FILTER_FLAG_ALLOW_HEX;
        }
        if ($allow_octal) {
            $filter_flags |= FILTER_FLAG_ALLOW_OCTAL;
        }

        $sanitized = [];

        // validate that each value of the array is a valid integer
        foreach ($values as $int) {
            $value = filter_var($int, FILTER_VALIDATE_INT, $filter_flags);

            if ($value === false || $int === true) {
                // filter_var validates bool TRUE to 1 (while bool FALSE is invalid) -.-
                $this->throwError('non_integer_value', [ 'value' => $int ]);
                return false;
            }

            // check minimum value
            if ($this->hasOption(IntegerListAttribute::OPTION_MIN, false)) {
                $min = filter_var(
                    $this->getOption(IntegerListAttribute::OPTION_MIN),
                    FILTER_VALIDATE_INT,
                    $filter_flags
                );

                if ($min === false) {
                    throw new InvalidConfigException('Minimum value specified is not interpretable as integer');
                }

                if ($value < $min) {
                    $this->throwError(IntegerListAttribute::OPTION_MIN, [
                        IntegerListAttribute::OPTION_MIN => $min,
                        'value' => $value
                    ]);
                    return false;
                }
            }

            // check maximum value
            if ($this->hasOption(IntegerListAttribute::OPTION_MAX, false)) {
                $max = filter_var(
                    $this->getOption(IntegerListAttribute::OPTION_MAX),
                    FILTER_VALIDATE_INT,
                    $filter_flags
                );

                if ($max === false) {
                    throw new InvalidConfigException('Maximum value specified is not interpretable as integer');
                }

                if ($value > $max) {
                    $this->throwError(IntegerListAttribute::OPTION_MAX, [
                        IntegerListAttribute::OPTION_MAX => $max,
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
