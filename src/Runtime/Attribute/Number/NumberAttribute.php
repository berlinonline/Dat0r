<?php

namespace Dat0r\Runtime\Attribute\Number;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Type\NumberRule;

class NumberAttribute extends Attribute
{
    const OPTION_MIN = 'min';
    const OPTION_MAX = 'max';

    public function getDefaultValue()
    {
        return (int)$this->getOption(self::OPTION_DEFAULT_VALUE, 0);
    }

    protected function buildValidationRules()
    {
        $options = [];
        if ($this->hasOption(self::OPTION_MIN)) {
            $options[self::OPTION_MIN] = $this->getOption(self::OPTION_MIN);
        }
        if ($this->hasOption(self::OPTION_MAX)) {
            $options[self::OPTION_MAX] = $this->getOption(self::OPTION_MAX);
        }
        $rules = new RuleList();
        $rules->push(new NumberRule('valid-number', $options));

        return $rules;
    }
}
