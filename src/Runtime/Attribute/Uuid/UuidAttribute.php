<?php

namespace Dat0r\Runtime\Attribute\Uuid;

use Dat0r\Runtime\Attribute\Text\TextAttribute;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Type\UuidRule;
use Rhumsaa\Uuid\Uuid as UuidGenerator;

class UuidAttribute extends TextAttribute
{
    const OPTION_TRIM = UuidRule::OPTION_TRIM;

    public static function generateVersion4()
    {
        return UuidGenerator::uuid4()->toString();
    }

    public function getNullValue()
    {
        return self::generateVersion4();
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $rules->push(
            new UuidRule('valid-uuidv4', $options)
        );

        return $rules;
    }
}
