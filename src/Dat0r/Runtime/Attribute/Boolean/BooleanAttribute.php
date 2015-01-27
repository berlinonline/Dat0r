<?php

namespace Dat0r\Runtime\Attribute\Boolean;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Rule\RuleList;

class BooleanAttribute extends Attribute
{
    public function getDefaultValue()
    {
        return (bool)$this->getOption(Attribute::OPTION_DEFAULT_VALUE, false);
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();
        $rules->push(new BooleanRule('valid-boolean'));

        return $rules;
    }
}
