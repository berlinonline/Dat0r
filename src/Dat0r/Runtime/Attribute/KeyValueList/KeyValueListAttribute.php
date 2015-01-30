<?php

namespace Dat0r\Runtime\Attribute\KeyValueList;

use Dat0r\Common\Error\InvalidConfigException;
use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\RuleList;

class KeyValueListAttribute extends Attribute
{
    const OPTION_CAST_VALUES_TO = 'cast_values_to';
    const OPTION_LITERALIZE = 'literalize';
    const OPTION_MAX = 'max';
    const OPTION_MIN = 'min';

    const CAST_TO_BOOLEAN = 'boolean';
    const CAST_TO_INTEGER = 'integer';
    const CAST_TO_NOTHING = 'nothing';
    const CAST_TO_STRING = 'string';

    public function getNullValue()
    {
        return [];
    }

    public function getDefaultValue()
    {
        $default_value = $this->getOption(self::OPTION_DEFAULT_VALUE, []);

        $validation_result = $this->getValidator()->validate($default_value);

        if ($validation_result->getSeverity() > IncidentInterface::NOTICE) {
            throw new InvalidConfigException(
                sprintf(
                    "Configured default_value '%s' for attribute '%s' on entity type '%s' is not valid.",
                    $default_value,
                    $this->getName(),
                    $this->getType() ? $this->getType()->getName() : 'undefined'
                )
            );
        }

        return $validation_result->getSanitizedValue();
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $rule = new KeyValueListRule('valid-key-value-list', $options);

        $rules->push($rule);

        return $rules;
    }
}
