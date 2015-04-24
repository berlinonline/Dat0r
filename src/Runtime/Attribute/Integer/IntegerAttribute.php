<?php

namespace Dat0r\Runtime\Attribute\Integer;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Type\IntegerRule;

class IntegerAttribute extends Attribute
{
    const OPTION_ALLOW_HEX      = IntegerRule::OPTION_ALLOW_HEX;
    const OPTION_ALLOW_OCTAL    = IntegerRule::OPTION_ALLOW_OCTAL;
    const OPTION_MIN_VALUE      = IntegerRule::OPTION_MIN_VALUE;
    const OPTION_MAX_VALUE      = IntegerRule::OPTION_MAX_VALUE;

    public function getNullValue()
    {
        return 0;
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $rules->push(new IntegerRule('valid-integer', $options));

        return $rules;
    }
}
