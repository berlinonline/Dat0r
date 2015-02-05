<?php

namespace Dat0r\Runtime\Attribute\BooleanList;

use Dat0r\Runtime\Attribute\ListAttribute;
use Dat0r\Runtime\Validator\Rule\RuleList;

class BooleanListAttribute extends ListAttribute
{
    protected function buildValidationRules()
    {
        $rules = parent::buildValidationRules();

        $options = $this->getOptions();

        $rule = new BooleanListRule('valid-boolean-list', $options);

        $rules->push($rule);

        return $rules;
    }
}
