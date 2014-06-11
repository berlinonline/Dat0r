<?php

namespace Dat0r\Runtime\ValueHolder\Type;

use Dat0r\Common\Error;
use Dat0r\Runtime\Attribute\IAttribute;
use Dat0r\Runtime\Attribute\Type\Text;

/**
 * Default IValueHolder implementation used for textarea value containment.
 */
class TextareaValueHolder extends TextValueHolder
{
    public function setValue($value)
    {
        // @todo move to validator
        $value = (string)$value;
        if ($this->getAttribute()->getOption('use_richtext', false)) {
            $value = html_entity_decode(
                htmlspecialchars_decode($value, ENT_COMPAT),
                ENT_COMPAT,
                'UTF-8'
            );
        }

        return parent::setValue($value);
    }
}
