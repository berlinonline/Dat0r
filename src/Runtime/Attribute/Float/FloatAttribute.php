<?php

namespace Dat0r\Runtime\Attribute\Float;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Type\FloatRule;

class FloatAttribute extends Attribute
{
    /**
     * Allow fraction separator (',' as in '1,200' === 1200)
     */
    const OPTION_ALLOW_THOUSAND_SEPARATOR = FloatRule::OPTION_ALLOW_THOUSAND_SEPARATOR;

    /**
     * precision when comparing two float values for equality. Falls back
     * to the php ini setting 'precision' (usually 14).
     */
    const OPTION_PRECISION_DIGITS = FloatRule::OPTION_PRECISION_DIGITS;

    /**
     * Whether of not to accept infinite float values. Please note, that
     * the toNative representation of infinite values is a special string
     * that is known by the validation rule to set infinity as the internal
     * value on reconstitution. This string is most likely neither valid nor
     * acceptable in other representation formats that are created upon the
     * toNative representation (e.g. json_encode and reading that value via
     * javascript and through sorcery hope that it's a float).
     */
    const OPTION_ALLOW_INFINITY = FloatRule::OPTION_ALLOW_INFINITY;

    /**
     * Whether of not to accept NAN float values. Please note, that
     * the toNative representation of not-a-number values is a special string
     * that is known by the validation rule to set NAN as the internal
     * value on reconstitution. This string is most likely neither valid nor
     * acceptable in other representation formats that are created upon the
     * toNative representation (e.g. json_encode and reading that value via
     * javascript and through sorcery hope that it's a float).
     */
    const OPTION_ALLOW_NAN = FloatRule::OPTION_ALLOW_NAN;

    const OPTION_MAX_VALUE = FloatRule::OPTION_MAX_VALUE;
    const OPTION_MIN_VALUE = FloatRule::OPTION_MIN_VALUE;

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
