<?php

namespace Dat0r\Runtime\Attribute\KeyValueList;

use Dat0r\Runtime\Attribute\ListAttribute;

/**
 * A list of key => value pairs where:
 *
 * - key is a string
 * - value is a string, integer, boolean or float
 *
 */
class KeyValueListAttribute extends ListAttribute
{
    const OPTION_ALLOWED_KEYS = 'allowed_keys';
    const OPTION_ALLOWED_VALUES = 'allowed_values';
    const OPTION_ALLOWED_PAIRS = 'allowed_pairs';

    /**
     * Option to define that values must be of a certain scalar type.
     */
    const OPTION_VALUE_TYPE = 'value_type';

    const VALUE_TYPE_BOOLEAN = 'boolean';
    const VALUE_TYPE_INTEGER = 'integer';
    const VALUE_TYPE_FLOAT = 'float';
    const VALUE_TYPE_DEFAULT = 'default';
    const VALUE_TYPE_TEXT = 'text';

    protected function buildValidationRules()
    {
        $rules = parent::buildValidationRules();

        $options = $this->getOptions();

        $rule = new KeyValueListRule('valid-key-value-list', $options);

        $rules->push($rule);

        return $rules;
    }
}
