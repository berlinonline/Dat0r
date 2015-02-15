<?php

namespace Dat0r\Runtime\Attribute\Url;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Type\UrlRule;

class UrlAttribute extends Attribute
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
            new UrlRule('valid-url', $options)
        );

        return $rules;
    }
}
