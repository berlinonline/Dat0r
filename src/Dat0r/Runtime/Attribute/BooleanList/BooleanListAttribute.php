<?php

namespace Dat0r\Runtime\Attribute\BooleanList;

use Dat0r\Runtime\Attribute\ListAttribute;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Type\ListRule;

class BooleanListAttribute extends ListAttribute
{
    protected function buildValidationRules()
    {
        $rules = parent::buildValidationRules();

        $options = $this->getOptions();

        $rules->push(new ListRule('valid-list', $options));
        $rules->push(new BooleanListRule('valid-boolean-list', $options));

        return $rules;
    }
}
