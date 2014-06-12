<?php

namespace Dat0r\Runtime\Attribute\Type;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Attribute\Validator\Rule\RuleList;
use Dat0r\Runtime\Attribute\Validator\Rule\Type\BooleanRule;

class Boolean extends Attribute
{
    public function getDefaultValue()
    {
        return (bool)$this->getOption('default_value', false);
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();
        $rules->push(new BooleanRule('valid-boolean'));

        return $rules;
    }
}
