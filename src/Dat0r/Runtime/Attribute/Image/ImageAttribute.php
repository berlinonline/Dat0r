<?php

namespace Dat0r\Runtime\Attribute\Image;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\RuleList;

class ImageAttribute extends Attribute
{
    const OPTION_ALLOWED_KEYS               = ImageRule::OPTION_ALLOWED_KEYS;
    const OPTION_ALLOWED_VALUES             = ImageRule::OPTION_ALLOWED_VALUES;
    const OPTION_ALLOWED_PAIRS              = ImageRule::OPTION_ALLOWED_PAIRS;

    /**
     * Option to define that values must be of a certain scalar type.
     */
    const OPTION_VALUE_TYPE                 = ImageRule::OPTION_VALUE_TYPE;

    const VALUE_TYPE_BOOLEAN                = ImageRule::VALUE_TYPE_BOOLEAN;
    const VALUE_TYPE_INTEGER                = ImageRule::VALUE_TYPE_INTEGER;
    const VALUE_TYPE_FLOAT                  = ImageRule::VALUE_TYPE_FLOAT;
    const VALUE_TYPE_SCALAR                 = ImageRule::VALUE_TYPE_SCALAR;
    const VALUE_TYPE_TEXT                   = ImageRule::VALUE_TYPE_TEXT;

    const OPTION_MAX_VALUE                  = ImageRule::OPTION_MAX_VALUE; // when value_type is float or int
    const OPTION_MIN_VALUE                  = ImageRule::OPTION_MIN_VALUE; // when value_type is float or int

    // text rule options
    const OPTION_ALLOW_CRLF                 = ImageRule::OPTION_ALLOW_CRLF;
    const OPTION_ALLOW_TAB                  = ImageRule::OPTION_ALLOW_TAB;
    const OPTION_MAX_LENGTH                 = ImageRule::OPTION_MAX_LENGTH;
    const OPTION_MIN_LENGTH                 = ImageRule::OPTION_MIN_LENGTH;
    const OPTION_NORMALIZE_NEWLINES         = ImageRule::OPTION_NORMALIZE_NEWLINES;
    const OPTION_REJECT_INVALID_UTF8        = ImageRule::OPTION_REJECT_INVALID_UTF8;
    const OPTION_STRIP_CONTROL_CHARACTERS   = ImageRule::OPTION_STRIP_CONTROL_CHARACTERS;
    const OPTION_STRIP_DIRECTION_OVERRIDES  = ImageRule::OPTION_STRIP_DIRECTION_OVERRIDES;
    const OPTION_STRIP_INVALID_UTF8         = ImageRule::OPTION_STRIP_INVALID_UTF8;
    const OPTION_STRIP_NULL_BYTES           = ImageRule::OPTION_STRIP_NULL_BYTES;
    const OPTION_STRIP_ZERO_WIDTH_SPACE     = ImageRule::OPTION_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_TRIM                       = ImageRule::OPTION_TRIM;

    // integer rule options
    const OPTION_ALLOW_HEX                  = ImageRule::OPTION_ALLOW_HEX;
    const OPTION_ALLOW_OCTAL                = ImageRule::OPTION_ALLOW_OCTAL;
    const OPTION_MAX_INTEGER_VALUE          = ImageRule::OPTION_MAX_INTEGER_VALUE;
    const OPTION_MIN_INTEGER_VALUE          = ImageRule::OPTION_MIN_INTEGER_VALUE;

    // float rule options
    const OPTION_ALLOW_THOUSAND_SEPARATOR   = ImageRule::OPTION_ALLOW_THOUSAND_SEPARATOR;
    const OPTION_PRECISION_DIGITS           = ImageRule::OPTION_PRECISION_DIGITS;
    const OPTION_ALLOW_INFINITY             = ImageRule::OPTION_ALLOW_INFINITY;
    const OPTION_ALLOW_NAN                  = ImageRule::OPTION_ALLOW_NAN;
    const OPTION_MAX_FLOAT_VALUE            = ImageRule::OPTION_MAX_FLOAT_VALUE;
    const OPTION_MIN_FLOAT_VALUE            = ImageRule::OPTION_MIN_FLOAT_VALUE;

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $rules->push(new ImageRule('valid-image', $options));

        return $rules;
    }
}
