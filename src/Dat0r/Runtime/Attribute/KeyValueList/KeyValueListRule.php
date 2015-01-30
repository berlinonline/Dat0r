<?php

namespace Dat0r\Runtime\Attribute\KeyValueList;

use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;

class KeyValueListRule extends Rule
{
    protected function execute($value)
    {
        if (!is_array($value)) {
            $this->throwError('non_array_value', [], IncidentInterface::CRITICAL);
            return false;
        }

        if (!empty($value) && !$this->isAssoc($value)) {
            $this->throwError('non_assoc_array', [], IncidentInterface::CRITICAL);
            return false;
        }

        $sanitized = [];

        foreach ($value as $key => $val) {
            $key = trim($key);
            if (empty($key)) {
                $this->throwError('empty_key', [], IncidentInterface::CRITICAL);
                return false;
            }

            if (!is_scalar($val)) {
                $this->throwError('non_scalar_value', [ 'key' => $key ], IncidentInterface::CRITICAL);
                return false;
            }

            // cast values to string, integer or boolean
            if ($this->hasOption(KeyValueListAttribute::OPTION_CAST_VALUES_TO)) {
                $val = $this->castValue($val);
            }

            // check minimum value or length
            if ($min = $this->getOption(KeyValueListAttribute::OPTION_MIN, false)) {
                if (is_string($val) && mb_strlen($val) < $min) {
                    $this->throwError(KeyValueListAttribute::OPTION_MIN, [
                        KeyValueListAttribute::OPTION_MIN => $min,
                        'value' => $val
                    ]);
                    return false;
                } elseif (is_int($val) && $val < (int)$min) {
                    $this->throwError(KeyValueListAttribute::OPTION_MIN, [
                        KeyValueListAttribute::OPTION_MIN => $min,
                        'value' => $val
                    ]);
                    return false;
                } else {
                    // misconfigured? ignore
                }
            }

            // check maximumaximum or length
            if ($max = $this->getOption(KeyValueListAttribute::OPTION_MAX, false)) {
                if (is_string($val) && mb_strlen($val) > $max) {
                    $this->throwError(KeyValueListAttribute::OPTION_MAX, [
                        KeyValueListAttribute::OPTION_MAX => $max,
                        'value_given' => $val
                    ]);
                    return false;
                } elseif (is_int($val) && $val > (int)$max) {
                    $this->throwError(KeyValueListAttribute::OPTION_MAX, [
                        KeyValueListAttribute::OPTION_MAX => $max,
                        'value_given' => $val
                    ]);
                    return false;
                } else {
                    // misconfigured? ignore
                }
            }

            $sanitized[$key] = $val;
        }

        $this->setSanitizedValue($sanitized);

        return true;
    }

    /**
     * @return bool true if argument is an associative array. False otherwise.
     */
    protected function isAssoc(array $array)
    {
        foreach (array_keys($array) as $key => $value) {
            if ($key !== $value) {
                return true;
            }
        }

        return false;
    }

    protected function castValue($value)
    {
        $value_type = $this->getOption(
            KeyValueListAttribute::OPTION_CAST_VALUES_TO,
            KeyValueListAttribute::CAST_TO_NOTHING
        );

        switch ($value_type) {
            case KeyValueListAttribute::CAST_TO_INTEGER:
                $value = (int)$value;
                break;

            case KeyValueListAttribute::CAST_TO_STRING:
                $value = (string)$value;
                break;

            case KeyValueListAttribute::CAST_TO_BOOLEAN:
                if (is_string($value) && $this->getOption(KeyValueListAttribute::OPTION_LITERALIZE, true)) {
                    $value = (bool)$this->toBoolean($value);
                } else {
                    $value = (bool)$value;
                }
                break;

            case KeyValueListAttribute::CAST_TO_NOTHING:
            default:
                break;
        }

        return $value;
    }

    protected function toBoolean($value)
    {
        if (!is_string($value)) {
            return false;
        }

        $value = trim($value);
        if ($value === '') {
            return true; //  TRUE as it is a string and by default PHP thinks of this as truthy
        }

        $value = strtolower($value);
        if ($value === 'on' || $value === 'yes' || $value === 'true') {
            return true;
        } elseif ($value === 'off' || $value === 'no' || $value === 'false') {
            return false;
        }

        return true; // all other strings are true (as PHP likes to think)
    }
}
