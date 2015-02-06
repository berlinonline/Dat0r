<?php

namespace Dat0r\Runtime\Attribute\KeyValueList;

use Dat0r\Runtime\Attribute\ListAttribute;

/**
 * A list of key => value pairs where:
 *
 * - key is a string
 * - value is a string, integer, boolean or anything
 *
 */
class KeyValueListAttribute extends ListAttribute
{
    const OPTION_ALLOWED_KEYS = 'allowed_keys';
    const OPTION_ALLOWED_VALUES = 'allowed_values';
    const OPTION_ALLOWED_PAIRS = 'allowed_pairs';

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
