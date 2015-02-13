<?php

namespace Dat0r\Runtime\Attribute\KeyValueList;

use Dat0r\Runtime\Attribute\Boolean\BooleanRule;
use Dat0r\Runtime\Attribute\Float\FloatAttribute;
use Dat0r\Runtime\Attribute\Float\FloatRule;
use Dat0r\Runtime\Attribute\Integer\IntegerAttribute;
use Dat0r\Runtime\Attribute\Integer\IntegerRule;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;
use Dat0r\Runtime\Validator\Rule\Type\TextRule;

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

        $value_type = $this->getOption(
            KeyValueListAttribute::OPTION_VALUE_TYPE,
            KeyValueListAttribute::VALUE_TYPE_DEFAULT
        );

        $allowed_values = [];
        if ($this->hasOption(KeyValueListAttribute::OPTION_ALLOWED_VALUES)) {
            $allowed_values = $this->getAllowedValues();
        }

        $allowed_keys = [];
        if ($this->hasOption(KeyValueListAttribute::OPTION_ALLOWED_KEYS)) {
            $allowed_keys = $this->getAllowedKeys();
        }

        $allowed_pairs = [];
        if ($this->hasOption(KeyValueListAttribute::OPTION_ALLOWED_PAIRS)) {
            $allowed_pairs = $this->getAllowedPairs();
        }

        $sanitized = [];

        $value_type = $this->getOption(
            KeyValueListAttribute::OPTION_VALUE_TYPE,
            KeyValueListAttribute::VALUE_TYPE_DEFAULT
        );

        $rule = null;
        switch ($value_type) {
            case KeyValueListAttribute::VALUE_TYPE_INTEGER:
                $rule = new IntegerRule('integer', $this->getOptions());
                break;
            case KeyValueListAttribute::VALUE_TYPE_FLOAT:
                $rule = new FloatRule('float', $this->getOptions());
                break;
                break;
            case KeyValueListAttribute::VALUE_TYPE_BOOLEAN:
                $rule = new BooleanRule('boolean', $this->getOptions());
                break;

            case KeyValueListAttribute::VALUE_TYPE_TEXT:
            case KeyValueListAttribute::VALUE_TYPE_DEFAULT:
            default:
                $rule = new TextRule('text', $this->getOptions());
                break;
        }

        foreach ($value as $key => $val) {
            $key = trim($key);
            if (empty($key)) {
                $this->throwError('empty_key', [], IncidentInterface::CRITICAL);
                return false;
            }

            // check for allowed keys
            if ($this->hasOption(KeyValueListAttribute::OPTION_ALLOWED_KEYS)) {
                if (!in_array($key, $allowed_keys, true)) {
                    $this->throwError(
                        KeyValueListAttribute::OPTION_ALLOWED_KEYS,
                        [
                            KeyValueListAttribute::OPTION_ALLOWED_KEYS => $allowed_keys,
                            'key' => $key
                        ]
                    );
                    return false;
                }
            }

            if (!is_scalar($val)) {
                $this->throwError('non_scalar_value', [ 'key' => $key ], IncidentInterface::CRITICAL);
                return false;
            }

            // we accept simple scalar types to be casted to strings
            if ($value_type === KeyValueListAttribute::VALUE_TYPE_TEXT) {
                $val = (string)$val;
            }

            // validate value to be string, integer, float or boolean
            if (!$rule->apply($val)) {
                $this->throwIncidents($rule);
                return false;
            }
            $val = $rule->getSanitizedValue();

            // check for allowed values
            if ($this->hasOption(KeyValueListAttribute::OPTION_ALLOWED_VALUES)) {
                // use FloatAttribute if equal value comparison of float values if important
                if (!in_array($val, $allowed_values, true)) {
                    $this->throwError(
                        KeyValueListAttribute::OPTION_ALLOWED_VALUES,
                        [
                            KeyValueListAttribute::OPTION_ALLOWED_VALUES => $allowed_values,
                            'value' => $val
                        ]
                    );
                    return false;
                }
            }

            // check for allowed key => values pairs
            if ($this->hasOption(KeyValueListAttribute::OPTION_ALLOWED_PAIRS)) {
                // use FloatAttribute if equal value comparison of float values is important (w/ precision)
                if (!(array_key_exists($key, $allowed_pairs) && $allowed_pairs[$key] === $val)) {
                    $this->throwError(
                        KeyValueListAttribute::OPTION_ALLOWED_PAIRS,
                        [
                            KeyValueListAttribute::OPTION_ALLOWED_PAIRS => $allowed_pairs,
                            'key' => $key,
                            'value' => $val
                        ]
                    );
                    return false;
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

    protected function throwIncidents($rule)
    {
        foreach ($rule->getIncidents() as $incident) {
            $this->throwError(
                $incident->getName(),
                $incident->getParameters(),
                $incident->getSeverity()
            );
        }
    }

    protected function getAllowedKeys()
    {
        $allowed_keys = [];

        $configured_allowed_keys = $this->getOption(KeyValueListAttribute::OPTION_ALLOWED_KEYS, []);
        if (!is_array($configured_allowed_keys)) {
            throw new InvalidConfigException('Configured allowed_keys must be an array of permitted key names.');
        }

        foreach ($configured_allowed_keys as $key) {
            $allowed_keys[] = (string)$key;
        }

        return $allowed_keys;
    }

    protected function getAllowedValues()
    {
        $configured_allowed_values = $this->getOption(KeyValueListAttribute::OPTION_ALLOWED_VALUES, []);
        if (!is_array($configured_allowed_values)) {
            throw new InvalidConfigException(
                'Configured allowed_values must be an array of permitted values.'
            );
        }

        return $this->castArray($configured_allowed_values);
    }

    protected function getAllowedPairs()
    {
        $configured_allowed_pairs = $this->getOption(KeyValueListAttribute::OPTION_ALLOWED_PAIRS, []);
        if (!is_array($configured_allowed_pairs)) {
            throw new InvalidConfigException(
                'Configured allowed_pairs must be an array of permitted key => value pairs.'
            );
        }

        return $this->castArray($configured_allowed_pairs);
    }

    protected function castArray($array)
    {
        $value_type = $this->getOption(
            KeyValueListAttribute::OPTION_VALUE_TYPE,
            KeyValueListAttribute::VALUE_TYPE_DEFAULT
        );

        $casted = [];

        foreach ($array as $key => $raw) {
            switch ($value_type) {
                case KeyValueListAttribute::VALUE_TYPE_INTEGER:
                    $casted_value = filter_var($raw, FILTER_VALIDATE_INT, $this->getIntegerFilterFlags());
                    if ($casted_value === false || $raw === true) {
                        throw new InvalidConfigException('Allowed integer values must be interpretable as integers.');
                    }
                    break;

                case KeyValueListAttribute::VALUE_TYPE_FLOAT:
                    $casted_value = filter_var($raw, FILTER_VALIDATE_FLOAT, $this->getFloatFilterFlags());
                    if ($casted_value === false || $raw === true) {
                        throw new InvalidConfigException(
                            'Allowed float values must be interpretable as floats. NAN or +-INF values are not ' .
                            'supported. The thousand separator (,) may be configured via attribute options.'
                        );
                    }
                    break;

                case KeyValueListAttribute::VALUE_TYPE_TEXT:
                    $casted_value = (string)$raw;
                    break;

                case KeyValueListAttribute::VALUE_TYPE_BOOLEAN:
                    $casted_value = $this->toBoolean($raw);
                    break;

                case KeyValueListAttribute::VALUE_TYPE_DEFAULT:
                default:
                    $casted_value = $raw;
                    break;
            }

            $casted[(string)$key] = $casted_value;
        }

        return $casted;

    }

    protected function getIntegerFilterFlags()
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

        return $filter_flags;
    }

    protected function getFloatFilterFlags()
    {
        $allow_thousand = $this->toBoolean(
            $this->getOption(FloatAttribute::OPTION_ALLOW_THOUSAND_SEPARATOR, false)
        );

        $filter_flags = 0;
        if ($allow_thousand) {
            $filter_flags |= FILTER_FLAG_ALLOW_THOUSAND;
        }

        return $filter_flags;
    }
}
