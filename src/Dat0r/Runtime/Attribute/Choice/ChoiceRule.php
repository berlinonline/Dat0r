<?php

namespace Dat0r\Runtime\Attribute\Choice;

use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;
use Dat0r\Runtime\Validator\Rule\Type\TextRule;

class ChoiceRule extends Rule
{
    protected function execute($value)
    {
        if (!is_string($value)) {
            $this->throwError('non_string_value', [], IncidentInterface::CRITICAL);
            return false;
        }

        $allowed_values = [];
        if ($this->hasOption(ChoiceAttribute::OPTION_ALLOWED_VALUES)) {
            $allowed_values = $this->getAllowedValues();
        }

        $text_rule = new TextRule('text', $this->getOptions());

        $is_valid = $text_rule->apply($value);
        if (!$is_valid) {
            foreach ($text_rule->getIncidents() as $incident) {
                $this->throwError($incident->getName(), $incident->getParameters(), $incident->getSeverity());
            }
            return false;
        } else {
            $value = $text_rule->getSanitizedValue();
        }

        // check for allowed values
        if ($this->hasOption(ChoiceAttribute::OPTION_ALLOWED_VALUES)) {
            if (!in_array($value, $allowed_values, true)) {
                $this->throwError(
                    ChoiceAttribute::OPTION_ALLOWED_VALUES,
                    [
                        ChoiceAttribute::OPTION_ALLOWED_VALUES => $allowed_values,
                        'value' => $value
                    ]
                );
                return false;
            }
        }

        $this->setSanitizedValue($value);

        return true;
    }

    protected function getAllowedValues()
    {
        $allowed_values = [];

        $configured_allowed_values = $this->getOption(ChoiceAttribute::OPTION_ALLOWED_VALUES, []);
        if (!is_array($allowed_values)) {
            throw new InvalidConfigException('Configured allowed_values must be an array of permitted values.');
        }

        foreach ($configured_allowed_values as $key => $raw) {
            $allowed_values[$key] = (string)$raw;
        }

        return $allowed_values;
    }
}
