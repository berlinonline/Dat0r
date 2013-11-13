<?php

namespace Dat0r\Runtime\Field;

use Dat0r\Runtime\Validation\Rule\RuleList;
use Dat0r\Runtime\Validation\Rule\BooleanRule;

class BooleanField extends Field
{
    public function getDefaultValue()
    {
        return (bool)$this->getOption('default_value', false);
    }

    protected function buildValidationRules()
    {
        return new RuleList(
            array('valid-boolean' => new BooleanRule('valid-boolean'))
        );
    }
}
