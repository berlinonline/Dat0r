<?php

namespace Dat0r\Runtime\Attribute\Bundle;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Bundle\NumberRule;

class Number extends Attribute
{
    public function getDefaultValue()
    {
        return (int)$this->getOption('default_value', 0);
    }

    protected function getValidationRules()
    {
        $options = array();
        if ($this->hasOption('min')) {
            $options['min'] = $this->getOption('min');
        }
        if ($this->hasOption('max')) {
            $options['max'] = $this->getOption('max');
        }

        $rules = new RuleList();
        $rules->push(new NumberRule('valid-number', $options));

        return $rules;
    }
}
