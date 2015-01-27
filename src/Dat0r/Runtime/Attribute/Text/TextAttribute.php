<?php

namespace Dat0r\Runtime\Attribute\Text;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Type\TextRule;

class TextAttribute extends Attribute
{
    public function getDefaultValue()
    {
        return (string)$this->getOption(Attribute::OPTION_DEFAULT_VALUE, '');
    }

    public function getNullValue()
    {
        return '';
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();
        $rules->push(
            new TextRule('valid-text', [ TextRule::OPTION_ENSURE_UTF8 => true, TextRule::OPTION_TRIM => true ])
        );

        $length_options = [];
        if ($this->hasOption(TextRule::OPTION_MIN)) {
            $length_options[TextRule::OPTION_MIN] = $this->getOption(TextRule::OPTION_MIN);
        }
        if ($this->hasOption(TextRule::OPTION_MAX)) {
            $length_options[TextRule::OPTION_MAX] = $this->getOption(TextRule::OPTION_MAX);
        }

        if (count($length_options) > 0) {
            $length_options[TextRule::OPTION_ENSURE_UTF8] = false;
            $length_options[TextRule::OPTION_TRIM] = false;
            $rules->push(new TextRule('valid-length', $length_options));
        }

        return $rules;
    }
}
