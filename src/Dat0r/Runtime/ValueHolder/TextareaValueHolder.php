<?php

namespace Dat0r\Runtime\ValueHolder;

use Dat0r\Common\Error;
use Dat0r\Runtime\Field\IField;
use Dat0r\Runtime\Field\TextField;

/**
 * Default IValueHolder implementation used for textarea value containment.
 */
class TextareaValueHolder extends TextValueHolder
{
    public function setValue($value)
    {
        // @todo move to validator
        $value = (string)$value;
        if ($this->getField()->getOption('use_richtext', false)) {
            $value = html_entity_decode(
                htmlspecialchars_decode($value, ENT_COMPAT),
                ENT_COMPAT,
                'UTF-8'
            );
        }

        return parent::setValue($value);
    }
}
