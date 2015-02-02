<?php

namespace Dat0r\Runtime\Attribute\IntegerList;

use Dat0r\Common\Error\InvalidConfigException;
use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\RuleList;

class IntegerListAttribute extends Attribute
{
    const OPTION_MAX = 'max';
    const OPTION_MIN = 'min';
    const OPTION_CAST_STRINGS_TO_INTEGER = 'cast_strings_to_integer';

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

        $rule = new IntegerListRule('valid-integer-list', $options);

        $rules->push($rule);

        return $rules;
    }
}
