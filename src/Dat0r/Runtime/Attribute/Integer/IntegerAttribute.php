<?php

namespace Dat0r\Runtime\Attribute\Integer;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Rule\RuleList;

class IntegerAttribute extends Attribute
{
    const OPTION_ALLOW_HEX = 'allow_hex';
    const OPTION_ALLOW_OCTAL = 'allow_octal';
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

        $rules->push(new IntegerRule('valid-integer', $options));

        return $rules;
    }
}
