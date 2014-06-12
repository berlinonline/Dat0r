<?php

namespace Dat0r\Runtime\Attribute\Bundle;

use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Bundle\EmailRule;

class Email extends Text
{
    protected function buildValidationRules()
    {
        $rules = new RuleList();
        $rules->push(new EmailRule('email-type'));

        return $rules;
    }
}
