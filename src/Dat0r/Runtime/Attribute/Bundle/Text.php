<?php

namespace Dat0r\Runtime\Attribute\Bundle;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Bundle\TextRule;

class Text extends Attribute
{
    public function getDefaultValue()
    {
        return (string)$this->getOption('default_value', '');
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();
        $rules->push(new TextRule('valid-text', array('ensure_utf8' => true, 'trim' => true)));

        $length_options = array();
        if ($this->hasOption('min')) {
            $length_options['min'] = $this->getOption('min');
        }
        if ($this->hasOption('max')) {
            $length_options['max'] = $this->getOption('max');
        }

        if (count($length_options) > 0) {
            $length_options['ensure_utf8'] = false;
            $length_options['trim'] = false;
            $rules->push(new TextRule('valid-length', $length_options));
        }

        return $rules;
    }
}
