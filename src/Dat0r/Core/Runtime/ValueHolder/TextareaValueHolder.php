<?php

namespace Dat0r\Core\Runtime\ValueHolder;

use Dat0r\Core\Runtime\Error;
use Dat0r\Core\Runtime\Field\IField;
use Dat0r\Core\Runtime\Field\TextField;

/**
 * Default IValueHolder implementation used for textarea value containment.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class TextareaValueHolder extends TextValueHolder
{
    public function setValue($value)
    {
        $value = (string)$value;

        if ($this->getField()->getOption('use_richtext', FALSE))
        {
            $value = html_entity_decode(htmlspecialchars_decode($value));
        }

        parent::setValue($value);
    }
}
