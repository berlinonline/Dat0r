<?php

namespace Dat0r\Runtime\Attribute\ImageList;

use Dat0r\Runtime\Attribute\ListAttribute;

/**
 * A list of images.
 */
class ImageListAttribute extends ListAttribute
{
    protected function buildValidationRules()
    {
        $rules = parent::buildValidationRules();

        $options = $this->getOptions();

        $rule = new ImageListRule('valid-image-list', $options);

        $rules->push($rule);

        return $rules;
    }
}
