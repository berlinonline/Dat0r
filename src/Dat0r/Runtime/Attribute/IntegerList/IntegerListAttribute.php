<?php

namespace Dat0r\Runtime\Attribute\IntegerList;

use Dat0r\Common\Error\InvalidConfigException;
use Dat0r\Runtime\Attribute\ListAttribute;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\RuleList;

class IntegerListAttribute extends ListAttribute
{
    const OPTION_ALLOW_HEX = 'allow_hex';
    const OPTION_ALLOW_OCTAL = 'allow_octal';
    const OPTION_MAX = 'max';
    const OPTION_MIN = 'min';

    protected function buildValidationRules()
    {
        $rules = parent::buildValidationRules();

        $options = $this->getOptions();

        $rule = new IntegerListRule('valid-integer-list', $options);

        $rules->push($rule);

        return $rules;
    }
}
