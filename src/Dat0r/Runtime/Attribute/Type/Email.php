<?php

namespace Dat0r\Runtime\Attribute\Type;

use Dat0r\Runtime\Attribute\Validator\Rule\RuleList;
use Dat0r\Runtime\Attribute\Validator\Rule\Type\EmailRule;

class Email extends Text
{
    protected function buildValidationRules()
    {
        $rules = new RuleList();
        $rules->push(new EmailRule('email-type'));

        return $rules;
    }
}
