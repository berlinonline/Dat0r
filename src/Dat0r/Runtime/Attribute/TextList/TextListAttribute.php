<?php

namespace Dat0r\Runtime\Attribute\TextList;

use Dat0r\Common\Error\InvalidConfigException;
use Dat0r\Runtime\Attribute\ListAttribute;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\RuleList;

class TextListAttribute extends ListAttribute
{
    const OPTION_MAX = 'max';
    const OPTION_MIN = 'min';
    const OPTION_TRIM = 'trim';
    const OPTION_ENSURE_UTF8 = 'ensure_utf8';

    protected function buildValidationRules()
    {
        $rules = parent::buildValidationRules();

        $options = $this->getOptions();

        $rule = new TextListRule('valid-text-list', $options);

        $rules->push($rule);

        return $rules;
    }
}
