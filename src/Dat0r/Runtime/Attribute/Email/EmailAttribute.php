<?php

namespace Dat0r\Runtime\Attribute\Email;

use Dat0r\Runtime\Attribute\Text\TextAttribute;
use Dat0r\Runtime\Validator\Rule\RuleList;

class EmailAttribute extends TextAttribute
{
    protected function buildValidationRules()
    {
        $rules = new RuleList();
        $rules->push(new EmailRule('email-type'));
        return $rules;
    }
}
