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
    /**
     * Allow fraction separator when casting to float (',' as in '1,200' === 1200)
     */
    const OPTION_ALLOW_THOUSAND_SEPARATOR = 'allow_thousand_separator';

    /**
     * Allow hexadecimal numbers when casting to integer
     */
    const OPTION_ALLOW_HEX = 'allow_hex';

    /**
     * Allow octal numbers when casting to integer
     */
    const OPTION_ALLOW_OCTAL = 'allow_octal';

    const OPTION_ALLOWED_KEYS = 'allowed_keys';
    const OPTION_ALLOWED_VALUES = 'allowed_values';
    const OPTION_ALLOWED_PAIRS = 'allowed_pairs';

    /**
     * Option to define that values must be casted to a specific type.
     */
    const OPTION_CAST_VALUES_TO = 'cast_values_to';

    /**
     * Minimum value or string length (depending values being int, string or float)
     */
    const OPTION_MAX = 'max';

    /**
     * Maximum value or string length (depending values being int, string or float)
     */
    const OPTION_MIN = 'min';

    const CAST_TO_BOOLEAN = 'boolean';
    const CAST_TO_INTEGER = 'integer';
    const CAST_TO_FLOAT = 'float';
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
