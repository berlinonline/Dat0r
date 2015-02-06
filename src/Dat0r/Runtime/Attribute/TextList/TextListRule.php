<?php

namespace Dat0r\Runtime\Attribute\TextList;

use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;

class TextListRule extends Rule
{
    protected function execute($values)
    {
        if (!is_array($values)) {
            $this->throwError('non_array_value', [], IncidentInterface::CRITICAL);
            return false;
        }

        $allowed_values = [];
        if ($this->hasOption(TextListAttribute::OPTION_ALLOWED_VALUES)) {
            $allowed_values = $this->getAllowedValues();
        }

        $sanitized = [];

        foreach ($values as $val) {
            if (!is_string($val)) {
                $this->throwError('non_string_value', [ 'value' => $val ], IncidentInterface::CRITICAL);
                return false;
            }

            // trim string value
            $ensure_utf8 = $this->getOption(TextListAttribute::OPTION_ENSURE_UTF8, false);
            if ($this->getOption(TextListAttribute::OPTION_TRIM, false)) {
                if ($ensure_utf8) {
                    $pattern = '/^[\pZ\pC]*+(?P<trimmed>.*?)[\pZ\pC]*+$/usDS';
                } else {
                    $pattern = '/^\s*+(?P<trimmed>.*?)\s*+$/sDS';
                }
                if (preg_match($pattern, $val, $matches)) {
                    $val = $matches['trimmed'];
                }
            }

            // check minimum string length
            if ($min = $this->getOption(TextListAttribute::OPTION_MIN, false)) {
                if (mb_strlen($val) < $min) {
                    $this->throwError(TextListAttribute::OPTION_MIN, [
                        TextListAttribute::OPTION_MIN => $min,
                        'value' => $val
                    ]);
                    return false;
                }
            }

            // check maximum string length
            if ($max = $this->getOption(TextListAttribute::OPTION_MAX, false)) {
                if (mb_strlen($val) > $max) {
                    $this->throwError(TextListAttribute::OPTION_MAX, [
                        TextListAttribute::OPTION_MAX => $max,
                        'value' => $val
                    ]);
                    return false;
                }
            }

            // check for allowed values
            if ($this->hasOption(TextListAttribute::OPTION_ALLOWED_VALUES)) {
                if (!in_array($val, $allowed_values, true)) {
                    $this->throwError(
                        TextListAttribute::OPTION_ALLOWED_VALUES,
                        [
                            TextListAttribute::OPTION_ALLOWED_VALUES => $allowed_values,
                            'value' => $val
                        ]
                    );
                    return false;
                }
            }

            $sanitized[] = $val;
        }

        $this->setSanitizedValue($sanitized);

        return true;
    }

    protected function getAllowedValues()
    {
        $allowed_values = [];

        $configured_allowed_values = $this->getOption(TextListAttribute::OPTION_ALLOWED_VALUES, []);
        if (!is_array($allowed_values)) {
            throw new InvalidConfigException('Configured allowed_values must be an array of permitted values.');
        }

        foreach ($configured_allowed_values as $key => $raw) {
            $allowed_values[$key] = (string)$raw;
        }

        return $allowed_values;
    }
}
