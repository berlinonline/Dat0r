<?php

namespace Dat0r\Runtime\Attribute\Type;

use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Type\InArrayRule;

class Choice extends Text
{
    protected $select_options;

    protected function buildValidationRules()
    {
        $rules = new RuleList();
        $rules->push(
            new InArrayRule(
                'valid-select-value',
                array(
                    'max' => $this->getOption('max', 0),
                    'allow_multiple' => $this->getOption('allow_multiple', false),
                    'cast_to_array' => $this->getOption('cast_to_array', true),
                    'allowed_values' => array_keys($this->getSelectOptions())
                )
            )
        );

        return $rules;
    }

    protected function getSelectOptions()
    {
        if (!$this->select_options) {
            $this->select_options = array();
            foreach ($this->getOption('select_options', array()) as $value => $label) {
                $this->select_options[$value] = $label;
            }

            if ($this->getOption('sort_options', false)) {
                asort($this->select_options);
            }
        }

        return $this->select_options;
    }
}
