<?php

namespace Dat0r\Runtime\Attribute\KeyValueList;

use Dat0r\Common\Error\InvalidConfigException;
use Dat0r\Runtime\Attribute\ListAttribute;
use Dat0r\Runtime\Validator\Rule\RuleList;

class KeyValueListAttribute extends ListAttribute
{
    const OPTION_CAST_VALUES_TO = 'cast_values_to';
    const OPTION_MAX = 'max';
    const OPTION_MIN = 'min';

    const CAST_TO_BOOLEAN = 'boolean';
    const CAST_TO_INTEGER = 'integer';
    const CAST_TO_NOTHING = 'nothing';
    const CAST_TO_STRING = 'string';

    protected function buildValidationRules()
    {
        $rules = parent::buildValidationRules();

        $options = $this->getOptions();

        $rule = new KeyValueListRule('valid-key-value-list', $options);

        $rules->push($rule);

        return $rules;
    }
}
