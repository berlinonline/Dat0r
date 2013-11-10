<?php

namespace Dat0r\Runtime\Field;

use Dat0r\Runtime\Validation\Rule\RuleList;
use Dat0r\Runtime\Validation\Rule\TextRule;

class TextField extends Field
{
    public function getDefaultValue()
    {
        return $this->hasOption(self::OPT_VALUE_DEFAULT)
            ? (string)$this->getOption(self::OPT_VALUE_DEFAULT)
            : '';
    }

    public function getValidationRules()
    {
        $rules = new RuleList(
            array('text-type' => new TextRule('text-type', array('ensure_utf8' => true, 'trim' => true)))
        );

        $length_options = array();
        if ($this->hasOption('min')) {
            $length_options['min'] = $this->getOption('min');
        }
        if ($this->hasOption('max')) {
            $length_options['max'] = $this->getOption('max');
        }

        if (count($length_options) > 0) {
            $rules->setItem('text-length', new TextRule('text-length', $length_options));
        }

        return $rules;
    }
}
