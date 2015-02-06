<?php

namespace Dat0r\Runtime\Attribute\Integer;

use Dat0r\Common\Error\InvalidConfigException;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;

class IntegerRule extends Rule
{
    protected function execute($value)
    {
        $allow_hex = $this->toBoolean($this->getOption(IntegerAttribute::OPTION_ALLOW_HEX, false));
        $allow_octal = $this->toBoolean($this->getOption(IntegerAttribute::OPTION_ALLOW_OCTAL, false));

        $filter_flags = 0;
        if ($allow_hex) {
            $filter_flags |= FILTER_FLAG_ALLOW_HEX;
        }
        if ($allow_octal) {
            $filter_flags |= FILTER_FLAG_ALLOW_OCTAL;
        }

        $sanitized = [];

        $int = filter_var($value, FILTER_VALIDATE_INT, $filter_flags);

        if ($int === false || $value === true) {
            // filter_var validates bool TRUE to 1 (while bool FALSE is invalid) -.-
            $this->throwError('non_integer_value', [ 'value' => $value ]);
            return false;
        }

        // check minimum value
        if ($this->hasOption(IntegerAttribute::OPTION_MIN)) {
            $min = filter_var($this->getOption(IntegerAttribute::OPTION_MIN), FILTER_VALIDATE_INT, $filter_flags);
            if ($min === false) {
                throw new InvalidConfigException('Minimum value specified is not interpretable as integer.');
            }

            if ($int < $min) {
                $this->throwError(IntegerAttribute::OPTION_MIN, [
                    IntegerAttribute::OPTION_MIN => $min,
                    'value' => $int
                ]);
                return false;
            }
        }

        // check maximum value
        if ($this->hasOption(IntegerAttribute::OPTION_MAX)) {
            $max = filter_var($this->getOption(IntegerAttribute::OPTION_MAX), FILTER_VALIDATE_INT, $filter_flags);
            if ($max === false) {
                throw new InvalidConfigException('Maximum value specified is not interpretable as integer.');
            }

            if ($int > $max) {
                $this->throwError(IntegerAttribute::OPTION_MAX, [
                    IntegerAttribute::OPTION_MAX => $max,
                    'value' => $int
                ]);
                return false;
            }
        }

        $this->setSanitizedValue($int);

        return true;
    }
}
