<?php

namespace Dat0r\Runtime\Attribute\Text;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Type\TextRule;

/**
 * Allows valid UTF8 texts, trims it, strips control characters except tabs and
 * newlines and spoofchecks the incoming and/or resulting text if wanted.
 * For valid options see TextRule and SpoofcheckerRule.
 */
class TextAttribute extends Attribute
{
    public function getNullValue()
    {
        return '';
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $rules->push(new TextRule('valid-text', $this->getOptions()));

        return $rules;
    }
}
