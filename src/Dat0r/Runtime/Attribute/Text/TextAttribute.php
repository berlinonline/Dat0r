<?php

namespace Dat0r\Runtime\Attribute\Text;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Type\TextRule;

class TextAttribute extends Attribute
{
    public function getNullValue()
    {
        return '';
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $rules->push(
            new TextRule('valid-text', $options)
        );

        return $rules;
    }
}
