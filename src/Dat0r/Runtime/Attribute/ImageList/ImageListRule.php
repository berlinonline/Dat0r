<?php

namespace Dat0r\Runtime\Attribute\ImageList;

use Dat0r\Runtime\Attribute\Image\ImageRule;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;
use Dat0r\Runtime\Entity\EntityInterface;

class ImageListRule extends Rule
{
    protected function execute($values, EntityInterface $entity = null)
    {
        if (!is_array($values)) {
            $this->throwError('non_array_value', [], IncidentInterface::CRITICAL);
            return false;
        }

        $sanitized = [];

        $image_rule = new ImageRule('image', $this->getOptions());

        foreach ($values as $val) {
            if (!$image_rule->apply($val)) {
                $this->throwIncidentsAsErrors($image_rule);
                return false;
            }

            $sanitized[] = $image_rule->getSanitizedValue();
        }

        $this->setSanitizedValue($sanitized);

        return true;
    }
}
