<?php

namespace Dat0r\Runtime\Attribute\TextList;

use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;
use Dat0r\Runtime\Validator\Rule\Type\TextRule;

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

        $text_rule = new TextRule('text', $this->getOptions());

        foreach ($values as $val) {
            $is_valid = $text_rule->apply($val);
            if (!$is_valid) {
                foreach ($text_rule->getIncidents() as $incident) {
                    $this->throwError($incident->getName(), $incident->getParameters(), $incident->getSeverity());
                }
                return false;
            } else {
                $val = $text_rule->getSanitizedValue();
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
