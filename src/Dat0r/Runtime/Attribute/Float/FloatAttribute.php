<?php

namespace Dat0r\Runtime\Attribute\Float;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Rule\RuleList;

class FloatAttribute extends Attribute
{
    /**
     * Allow fraction separator (',' as in '1,200' === 1200)
     */
    const OPTION_ALLOW_THOUSAND_SEPARATOR = 'allow_thousand_separator';

    /**
     * precision when comparing two float values for equality. Falls back
     * to the php ini setting 'precision' (usually 14).
     */
    const OPTION_PRECISION_DIGITS = 'precision_digits';

    /**
     * Whether of not to accept infinite float values. Please note, that
     * the toNative representation of infinite values is a special string
     * that is known by the validation rule to set infinity as the internal
     * value on reconstitution. This string is most likely neither valid nor
     * acceptable in other representation formats that are created upon the
     * toNative representation (e.g. json_encode and reading that value via
     * javascript and through sorcery hope that it's a float).
     */
    const OPTION_ALLOW_INFINITY = 'allow_infinity';

    /**
     * Whether of not to accept NAN float values. Please note, that
     * the toNative representation of not-a-number values is a special string
     * that is known by the validation rule to set NAN as the internal
     * value on reconstitution. This string is most likely neither valid nor
     * acceptable in other representation formats that are created upon the
     * toNative representation (e.g. json_encode and reading that value via
     * javascript and through sorcery hope that it's a float).
     */
    const OPTION_ALLOW_NAN = 'allow_nan';

    const OPTION_MIN = 'min';
    const OPTION_MAX = 'max';

    public function getNullValue()
    {
        return 0;
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $rules->push(new FloatRule('valid-float', $options));

        return $rules;
    }
}
