<?php

namespace Dat0r\Runtime\Attribute\Choice;

use Dat0r\Common\Error\InvalidConfigException;
use Dat0r\Runtime\Attribute\Text\TextAttribute;

/**
 * A string out of a list of allowed strings.
 */
class ChoiceAttribute extends TextAttribute
{
    const OPTION_ALLOWED_VALUES = 'allowed_values';

    public function getNullValue()
    {
        return '';
    }

    protected function buildValidationRules()
    {
        $rules = parent::buildValidationRules();

        $options = $this->getOptions();

        $rules->push(new ChoiceRule('valid-text', $this->getOptions()));

        return $rules;
    }
}
