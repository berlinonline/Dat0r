<?php

namespace Dat0r\Runtime\Field\Type;

use Dat0r\Runtime\Field\Field;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Type\BooleanRule;

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
