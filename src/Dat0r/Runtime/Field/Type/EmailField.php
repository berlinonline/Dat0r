<?php

namespace Dat0r\Runtime\Field\Type;

use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Type\EmailRule;

class EmailField extends TextField
{
    protected function buildValidationRules()
    {
        $rules = new RuleList();
        $rules->push(new EmailRule('email-type'));

        return $rules;
    }
}
