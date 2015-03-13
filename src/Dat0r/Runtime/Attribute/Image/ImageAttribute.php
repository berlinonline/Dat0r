<?php

namespace Dat0r\Runtime\Attribute\Image;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\RuleList;

class ImageAttribute extends Attribute
{
    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $rules->push(new ImageRule('valid-image', $options));

        return $rules;
    }
}
