<?php

namespace Dat0r\Runtime\Field;

use Dat0r\Runtime\Validation\Rule\RuleList;
use Dat0r\Runtime\Validation\Rule\EmailRule;

class EmailField extends TextField
{
    protected function buildValidationRules()
    {
        return new RuleList(
            array('valid-email' => new EmailRule('email-type'))
        );
    }
}
