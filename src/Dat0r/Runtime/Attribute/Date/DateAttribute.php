<?php

namespace Dat0r\Runtime\Attribute\Date;

use Dat0r\Runtime\Attribute\Timestamp\TimestampAttribute;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\RuleList;

// preferred exchange format is FORMAT_ISO8601 ('Y-m-d\TH:i:s.uP')
class DateAttribute extends TimestampAttribute
{
    const OPTION_DEFAULT_HOUR = 'default_hour';
    const OPTION_DEFAULT_MINUTE = 'default_minute';
    const OPTION_DEFAULT_SECOND = 'default_second';

    const FORMAT_NATIVE = TimestampAttribute::FORMAT_ISO8601_SIMPLE;

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $valid_date_rule = new DateRule('valid-date', $options);

        $rules->push($valid_date_rule);

        return $rules;
    }
}
