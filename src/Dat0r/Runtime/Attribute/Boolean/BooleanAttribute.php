<?php

namespace Dat0r\Runtime\Attribute\Boolean;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Rule\RuleList;

class BooleanAttribute extends Attribute
{
    public function getNullValue()
    {
        $value = $this->getOption(Attribute::OPTION_NULL_VALUE, false);

        $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if (null === $bool) {
            return false;
        }

        return $bool;
    }

    public function getDefaultValue()
    {
        $value = $this->getOption(Attribute::OPTION_DEFAULT_VALUE, false);

        $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if (null === $bool) {
            return $this->getNullValue();
        }

        return $bool;
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $rules->push(new BooleanRule('valid-boolean'));

        return $rules;
    }
}
