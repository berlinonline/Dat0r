<?php

namespace Dat0r\Runtime\Attribute\Float;

use Dat0r\Common\Error\InvalidConfigException;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;

class FloatRule extends Rule
{
    protected function execute($value)
    {
        $allow_thousand = $this->toBoolean($this->getOption(FloatAttribute::OPTION_ALLOW_THOUSAND_SEPARATOR, false));
        $allow_infinity = $this->toBoolean($this->getOption(FloatAttribute::OPTION_ALLOW_INFINITY, false));
        $allow_nan = $this->toBoolean($this->getOption(FloatAttribute::OPTION_ALLOW_NAN, false));

        $filter_flags = 0;
        if ($allow_thousand) {
            $filter_flags |= FILTER_FLAG_ALLOW_THOUSAND;
        }

        $sanitized = [];

        $float = filter_var($value, FILTER_VALIDATE_FLOAT, $filter_flags);

        if ($float === false || $value === true) {
            // float(NAN), float(INF) and float(-INF) are invalid floats according to filter_var
            if (!is_string($value) && !is_float($value)) {
                $this->throwError('non_float_value', [ 'value' => $value ]);
                return false;
            }

            // floats/strings 'NAN', 'INF' and '-INF' may be valid for us if attribute options say so
            // strings will be set with 'real' float values instead of staying strings
            $strval = "$value";
            if ($strval === 'INF') {
                if (!$allow_infinity) {
                    $this->throwError('float_value_infinity', [ 'value' => $value ]);
                    return false;
                }
                $float = -log(0);
            } elseif ($strval === '-INF') {
                if (!$allow_infinity) {
                    $this->throwError('float_value_infinity', [ 'value' => $value ]);
                    return false;
                }
                $float = log(0);
            } elseif ($strval === 'NAN') {
                if (!$allow_nan) {
                    $this->throwError('float_value_nan', [ 'value' => $value ]);
                    return false;
                }
                $float = acos(1.01);
            } else {
                // not valid according to filter_var, not TRUE, not allowed NAN or INF value
                $this->throwError('non_float_value', [ 'value' => $value ]);
                return false;
            }
        }

        // check for NAN value (in case filter_var accepts those as valid in the future…)
        if (is_nan($float) && !$allow_nan) {
            $this->throwError('float_value_nan', [ 'value' => $float ]);
            return false;
        }

        // check for INFINITE value (in case filter_var accepts those as valid in the future…)
        if (is_infinite($float) && !$allow_infinity) {
            $this->throwError('float_value_infinity', [ 'value' => $float ]);
            return false;
        }

        // check minimum value
        if ($this->hasOption(FloatAttribute::OPTION_MIN)) {
            $min = filter_var($this->getOption(FloatAttribute::OPTION_MIN), FILTER_VALIDATE_FLOAT, $filter_flags);
            if ($min === false) {
                throw new InvalidConfigException('Minimum value specified is not interpretable as float.');
            }

            if ($float < $min) {
                $this->throwError(FloatAttribute::OPTION_MIN, [
                    FloatAttribute::OPTION_MIN => $min,
                    'value' => $float
                ]);
                return false;
            }
        }

        // check maximum value
        if ($this->hasOption(FloatAttribute::OPTION_MAX)) {
            $max = filter_var($this->getOption(FloatAttribute::OPTION_MAX), FILTER_VALIDATE_FLOAT, $filter_flags);
            if ($max === false) {
                throw new InvalidConfigException('Maximum value specified is not interpretable as float.');
            }

            if ($float > $max) {
                $this->throwError(FloatAttribute::OPTION_MAX, [
                    FloatAttribute::OPTION_MAX => $max,
                    'value' => $float
                ]);
                return false;
            }
        }

        $this->setSanitizedValue($float);

        return true;
    }
}
