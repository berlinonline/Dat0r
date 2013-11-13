<?php

namespace Dat0r\Runtime\Field;

use Dat0r\Runtime\Validation\Rule\RuleList;
use Dat0r\Runtime\Validation\Rule\NumberRule;

class IntegerField extends Field
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

        return new RuleList(
            array('valid-number' => new NumberRule('valid-number', $options))
        );
    }
}
