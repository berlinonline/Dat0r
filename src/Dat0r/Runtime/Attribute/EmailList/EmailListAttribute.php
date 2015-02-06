<?php

namespace Dat0r\Runtime\Attribute\EmailList;

use Dat0r\Runtime\Attribute\ListAttribute;
use Dat0r\Runtime\Validator\Rule\RuleList;

/**
 * A list of key => value pairs where:
 *
 * - key is an email string
 * - value is a string (label/name)
 *
 */
class EmailListAttribute extends ListAttribute
{
    const OPTION_ALLOWED_EMAILS = 'allowed_emails';
    const OPTION_ALLOWED_LABELS = 'allowed_labels';
    const OPTION_ALLOWED_PAIRS = 'allowed_pairs';

    const OPTION_MIN = 'min';
    const OPTION_MAX = 'max';

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $rule = new EmailListRule('valid-email-list', $options);

        $rules->push($rule);

        return $rules;
    }
}
